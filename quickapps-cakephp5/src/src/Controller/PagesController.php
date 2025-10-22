<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Service\VacationCalculator;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\View\Exception\MissingTemplateException;
use Exception;

/**
 * Static content controller
 *
 * This controller will render views from templates/Pages/
 *
 * @link https://book.cakephp.org/4/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{
    /**
     * Home page with vacation calculator
     *
     * @return void
     */
    public function home()
    {
        $result = null;
        $errors = [];

        // Process form submission
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            try {
                // Validate and parse input data
                $hireDate = new DateTime($data['hire_date'] ?? '');
                $baseDaysPerYear = (int)($data['base_days'] ?? 20);
                $calculationDate = !empty($data['calculation_date'])
                    ? new DateTime($data['calculation_date'])
                    : new DateTime('now');

                // Parse carryover parameter (optional)
                $daysUsedLastYear = null;
                if (isset($data['days_used_last_year']) && $data['days_used_last_year'] !== '') {
                    $daysUsedLastYear = (int)$data['days_used_last_year'];
                }

                // Create calculator and perform calculation
                $calculator = new VacationCalculator();
                $availableDays = $calculator->calculateAvailableDays(
                    $hireDate,
                    $baseDaysPerYear,
                    $calculationDate,
                    $daysUsedLastYear,
                );

                // Calculate detailed information for display
                $interval = $hireDate->diff($calculationDate);
                $yearsWorked = $interval->y;
                $monthsWorked = $interval->m;
                $totalMonths = ($yearsWorked * 12) + $monthsWorked;
                if ($interval->d > 0) {
                    $totalMonths++;
                }

                // Calculate base days (proportional)
                $effectiveMonths = min($totalMonths, 12);
                $daysPerMonth = (float)$baseDaysPerYear / 12.0;
                $baseDays = (int)round($daysPerMonth * (float)$effectiveMonths);

                // Calculate seniority bonus
                $seniorityMilestones = (int)floor($yearsWorked / 5);
                $seniorityBonus = $seniorityMilestones * 5;

                // Calculate carryover days (if provided)
                $carryoverDays = null;
                if ($daysUsedLastYear !== null) {
                    $carryoverDays = max(0, $baseDaysPerYear - $daysUsedLastYear);
                }

                $result = [
                    'hire_date' => $hireDate->i18nFormat('Y-MM-dd'),
                    'calculation_date' => $calculationDate->i18nFormat('Y-MM-dd'),
                    'years_worked' => $yearsWorked,
                    'months_worked' => $monthsWorked,
                    'base_days_per_year' => $baseDaysPerYear,
                    'base_days' => $baseDays,
                    'seniority_bonus' => $seniorityBonus,
                    'carryover_days' => $carryoverDays,
                    'days_used_last_year' => $daysUsedLastYear,
                    'total_days' => $availableDays,
                ];
            } catch (Exception $e) {
                $errors[] = 'Calculation error: ' . $e->getMessage();
            }
        }

        $this->set(compact('result', 'errors'));
    }

    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\View\Exception\MissingTemplateException When the view file could not
     *   be found and in debug mode.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found and not in debug mode.
     * @throws \Cake\View\Exception\MissingTemplateException In debug mode.
     */
    public function display(string ...$path): ?Response
    {
        if (!$path) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new NotFoundException();
        }
    }
}
