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
 * - Carryover days: unused vacation days from the previous year
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
     * Number of months in a year
     */
    private const MONTHS_PER_YEAR = 12;

    /**
     * Calculate available vacation days for an employee
     *
     * Calculates the total vacation days available by combining:
     * - Proportional base days (based on months worked this year)
     * - Seniority bonus (based on years of employment)
     * - Carryover days (unused vacation from previous year, if provided)
     *
     * @param \DateTimeInterface $hireDate Employee's hire date
     * @param int $baseDaysPerYear Base vacation days per year (e.g., 20)
     * @param \DateTimeInterface $calculationDate Date to calculate vacation days for (usually today)
     * @param int|null $daysUsedLastYear Days used in the previous year (for carryover calculation).
     *                                   If null, no carryover is calculated.
     * @return int Total available vacation days
     */
    public function calculateAvailableDays(
        DateTimeInterface $hireDate,
        int $baseDaysPerYear,
        DateTimeInterface $calculationDate,
        ?int $daysUsedLastYear = null,
    ): int {
        // Validate: calculation date must be on or after hire date
        if ($calculationDate < $hireDate) {
            return 0;
        }

        // Validate: base days per year cannot be negative
        if ($baseDaysPerYear < 0) {
            $baseDaysPerYear = 0;
        }

        // Calculate months worked
        $monthsWorked = $this->calculateMonthsWorked($hireDate, $calculationDate);

        // Calculate proportional base days (based on months worked in current year)
        $baseDays = $this->calculateProportionalDays($baseDaysPerYear, $monthsWorked);

        // Calculate seniority bonus
        $seniorityBonus = $this->calculateSeniorityBonus($hireDate, $calculationDate);

        // Calculate carryover days from previous year
        // Only applied when $daysUsedLastYear is provided (not null)
        // Carryover = unused days from last year (cannot be negative)
        $carryoverDays = 0;
        if ($daysUsedLastYear !== null) {
            $carryoverDays = max(0, $baseDaysPerYear - $daysUsedLastYear);
        }

        return $baseDays + $seniorityBonus + $carryoverDays;
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
        $totalMonths = ($interval->y * self::MONTHS_PER_YEAR) + $interval->m;

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
        $effectiveMonths = min($monthsWorked, self::MONTHS_PER_YEAR);

        // Calculate proportional days (rounded)
        $daysPerMonth = (float)$baseDaysPerYear / (float)self::MONTHS_PER_YEAR;

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
