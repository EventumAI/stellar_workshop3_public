<?php
declare(strict_types=1);

namespace App\Service;

use DateTimeInterface;

/**
 * Vacation Calculator Service
 *
 * Calculates available vacation days for employees based on:
 * - Base vacation days per year (proportional to months worked)
 * - Seniority bonus: +5 days for every 5 years of employment
 */
class VacationCalculator
{
    /**
     * Days added per seniority milestone (every 5 years)
     */
    private const SENIORITY_BONUS_DAYS = 5;

    /**
     * Years required for each seniority bonus
     */
    private const SENIORITY_MILESTONE_YEARS = 5;

    /**
     * Calculate available vacation days for an employee
     *
     * @param \DateTimeInterface $hireDate Employee's hire date
     * @param int $baseDaysPerYear Base vacation days per year (e.g., 20)
     * @param \DateTimeInterface $calculationDate Date to calculate vacation days for (usually today)
     * @return int Total available vacation days
     */
    public function calculateAvailableDays(
        DateTimeInterface $hireDate,
        int $baseDaysPerYear,
        DateTimeInterface $calculationDate,
    ): int {
        // Calculate months worked
        $monthsWorked = $this->calculateMonthsWorked($hireDate, $calculationDate);

        // Calculate proportional base days (based on months worked in current year)
        $baseDays = $this->calculateProportionalDays($baseDaysPerYear, $monthsWorked);

        // Calculate seniority bonus
        $seniorityBonus = $this->calculateSeniorityBonus($hireDate, $calculationDate);

        return $baseDays + $seniorityBonus;
    }

    /**
     * Calculate total months worked from hire date to calculation date
     *
     * @param \DateTimeInterface $hireDate Employee's hire date
     * @param \DateTimeInterface $calculationDate Calculation date
     * @return int Total months worked
     */
    private function calculateMonthsWorked(DateTimeInterface $hireDate, DateTimeInterface $calculationDate): int
    {
        $interval = $hireDate->diff($calculationDate);
        $totalMonths = ($interval->y * 12) + $interval->m;

        // Add 1 if there are any days in the partial month
        if ($interval->d > 0) {
            $totalMonths++;
        }

        return $totalMonths;
    }

    /**
     * Calculate proportional vacation days based on months worked
     *
     * For the current year, calculate days proportionally:
     * - If worked 12+ months: full base days
     * - If worked < 12 months: proportional calculation
     *
     * @param int $baseDaysPerYear Base vacation days per year
     * @param int $monthsWorked Total months worked
     * @return int Proportional vacation days
     */
    private function calculateProportionalDays(int $baseDaysPerYear, int $monthsWorked): int
    {
        // Cap at 12 months for annual calculation
        $effectiveMonths = min($monthsWorked, 12);

        // Calculate proportional days (rounded)
        $daysPerMonth = (float)$baseDaysPerYear / 12.0;

        return (int)round($daysPerMonth * (float)$effectiveMonths);
    }

    /**
     * Calculate seniority bonus based on years of employment
     *
     * Adds SENIORITY_BONUS_DAYS for every SENIORITY_MILESTONE_YEARS completed
     * Example: 5 years = +5 days, 10 years = +10 days, etc.
     *
     * @param \DateTimeInterface $hireDate Employee's hire date
     * @param \DateTimeInterface $calculationDate Calculation date
     * @return int Total seniority bonus days
     */
    private function calculateSeniorityBonus(DateTimeInterface $hireDate, DateTimeInterface $calculationDate): int
    {
        $interval = $hireDate->diff($calculationDate);
        $yearsWorked = $interval->y;

        // Calculate number of completed seniority milestones
        $milestones = (int)floor($yearsWorked / self::SENIORITY_MILESTONE_YEARS);

        return $milestones * self::SENIORITY_BONUS_DAYS;
    }
}
