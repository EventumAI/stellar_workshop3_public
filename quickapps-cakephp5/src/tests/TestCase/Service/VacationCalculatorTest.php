<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\VacationCalculator;
use Cake\TestSuite\TestCase;
use DateTime;

/**
 * VacationCalculator Test Case
 */
class VacationCalculatorTest extends TestCase
{
    private VacationCalculator $calculator;

    /**
     * setUp method
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new VacationCalculator();
    }

    /**
     * tearDown method
     */
    protected function tearDown(): void
    {
        unset($this->calculator);
        parent::tearDown();
    }

    /**
     * Test new employee - worked 1 month
     */
    public function testNewEmployeeOneMonth(): void
    {
        $hireDate = new DateTime('2024-09-01');
        $calculationDate = new DateTime('2024-10-01');
        $baseDaysPerYear = 24; // 24 days per year = 2 days per month

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 1 month worked: 24/12 * 1 = 2 days
        $this->assertEquals(2, $result);
    }

    /**
     * Test employee worked 6 months (half year)
     */
    public function testEmployeeWorkedSixMonths(): void
    {
        $hireDate = new DateTime('2024-04-01');
        $calculationDate = new DateTime('2024-10-01');
        $baseDaysPerYear = 24;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 6 months worked: 24/12 * 6 = 12 days
        $this->assertEquals(12, $result);
    }

    /**
     * Test employee worked full year
     */
    public function testEmployeeWorkedFullYear(): void
    {
        $hireDate = new DateTime('2023-01-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 20;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 12 months worked: 20 days base, no seniority bonus yet
        $this->assertEquals(20, $result);
    }

    /**
     * Test employee with 5 years seniority (one bonus)
     */
    public function testEmployeeWithFiveYearsSeniority(): void
    {
        $hireDate = new DateTime('2019-01-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 20;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 5 years = 20 base days + 5 seniority bonus = 25 days
        $this->assertEquals(25, $result);
    }

    /**
     * Test employee with 10 years seniority (two bonuses)
     */
    public function testEmployeeWithTenYearsSeniority(): void
    {
        $hireDate = new DateTime('2014-01-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 20;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 10 years = 20 base days + 10 seniority bonus (2 milestones × 5) = 30 days
        $this->assertEquals(30, $result);
    }

    /**
     * Test employee with 15 years seniority (three bonuses)
     */
    public function testEmployeeWithFifteenYearsSeniority(): void
    {
        $hireDate = new DateTime('2009-01-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 20;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 15 years = 20 base days + 15 seniority bonus (3 milestones × 5) = 35 days
        $this->assertEquals(35, $result);
    }

    /**
     * Test employee with 4 years and 11 months (just before 5-year milestone)
     */
    public function testEmployeeJustBeforeFiveYears(): void
    {
        $hireDate = new DateTime('2019-02-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 20;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 4 years 11 months = 20 base days + 0 seniority bonus = 20 days
        $this->assertEquals(20, $result);
    }

    /**
     * Test proportional calculation with partial month
     */
    public function testProportionalCalculationWithPartialMonth(): void
    {
        $hireDate = new DateTime('2024-08-15');
        $calculationDate = new DateTime('2024-10-21');
        $baseDaysPerYear = 24;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // From Aug 15 to Oct 21 = 2 months + 6 days = 3 months (rounded up)
        // 24/12 * 3 = 6 days
        $this->assertEquals(6, $result);
    }

    /**
     * Test employee with 7 years seniority
     */
    public function testEmployeeWithSevenYearsSeniority(): void
    {
        $hireDate = new DateTime('2017-01-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 24;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 7 years = 24 base days + 5 seniority bonus (1 milestone) = 29 days
        $this->assertEquals(29, $result);
    }

    /**
     * Test with different base days per year
     */
    public function testDifferentBaseDaysPerYear(): void
    {
        $hireDate = new DateTime('2023-01-01');
        $calculationDate = new DateTime('2024-01-01');

        // Test with 18 days base
        $result18 = $this->calculator->calculateAvailableDays($hireDate, 18, $calculationDate);
        $this->assertEquals(18, $result18);

        // Test with 30 days base
        $result30 = $this->calculator->calculateAvailableDays($hireDate, 30, $calculationDate);
        $this->assertEquals(30, $result30);
    }

    // ========================================
    // Corner Case Tests
    // ========================================

    /**
     * Test calculation date is before hire date (negative employment period)
     */
    public function testCalculationDateBeforeHireDate(): void
    {
        $hireDate = new DateTime('2024-10-01');
        $calculationDate = new DateTime('2024-01-01'); // Before hire date!

        $result = $this->calculator->calculateAvailableDays($hireDate, 20, $calculationDate);

        // Employee not hired yet = no vacation days
        $this->assertEquals(0, $result);
    }

    /**
     * Test with zero base days per year
     */
    public function testZeroBaseDaysPerYear(): void
    {
        $hireDate = new DateTime('2023-01-01');
        $calculationDate = new DateTime('2024-01-01');

        $result = $this->calculator->calculateAvailableDays($hireDate, 0, $calculationDate);

        // With 0 base days, should get 0 total (even with seniority)
        $this->assertEquals(0, $result);
    }

    /**
     * Test with negative base days per year
     */
    public function testNegativeBaseDaysPerYear(): void
    {
        $hireDate = new DateTime('2023-01-01');
        $calculationDate = new DateTime('2024-01-01');

        $result = $this->calculator->calculateAvailableDays($hireDate, -10, $calculationDate);

        // Negative base days should be treated as 0
        $this->assertEquals(0, $result);
    }

    /**
     * Test employee hired today (zero days of employment)
     */
    public function testEmployeeHiredToday(): void
    {
        $today = new DateTime('2024-10-21');

        $result = $this->calculator->calculateAvailableDays($today, 20, $today);

        // Employee hired today gets 0 days (hasn't worked yet)
        $this->assertEquals(0, $result);
    }

    /**
     * Test employee with very long service (50 years)
     */
    public function testEmployeeWithFiftyYearsService(): void
    {
        $hireDate = new DateTime('1974-01-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 20;

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 50 years = 10 milestones × 5 days = 50 bonus days
        // 20 base + 50 bonus = 70 days
        $this->assertEquals(70, $result);
    }

    /**
     * Test with very large base days per year
     */
    public function testVeryLargeBaseDaysPerYear(): void
    {
        $hireDate = new DateTime('2023-01-01');
        $calculationDate = new DateTime('2024-01-01');
        $baseDaysPerYear = 365; // Unrealistic but possible input

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // Should handle gracefully
        $this->assertEquals(365, $result);
    }

    /**
     * Test with odd number of base days that don't divide evenly by 12
     */
    public function testOddBaseDaysWithRounding(): void
    {
        $hireDate = new DateTime('2024-04-01');
        $calculationDate = new DateTime('2024-10-01');
        $baseDaysPerYear = 13; // 13/12 = 1.0833... per month

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 6 months worked: 13/12 * 6 = 6.5, rounded = 7 days
        $this->assertEquals(7, $result);
    }

    /**
     * Test with base days that cause rounding down
     */
    public function testBaseDaysWithRoundingDown(): void
    {
        $hireDate = new DateTime('2024-09-01');
        $calculationDate = new DateTime('2024-10-01');
        $baseDaysPerYear = 13; // 13/12 = 1.0833... per month

        $result = $this->calculator->calculateAvailableDays($hireDate, $baseDaysPerYear, $calculationDate);

        // 1 month worked: 13/12 * 1 = 1.0833..., rounded = 1 day
        $this->assertEquals(1, $result);
    }

    /**
     * Test calculation on exact hire date anniversary
     */
    public function testExactHireDateAnniversary(): void
    {
        $hireDate = new DateTime('2019-01-01');
        $calculationDate = new DateTime('2024-01-01'); // Exactly 5 years

        $result = $this->calculator->calculateAvailableDays($hireDate, 20, $calculationDate);

        // Exactly 5 years = 20 base + 5 seniority = 25 days
        $this->assertEquals(25, $result);
    }

    /**
     * Test one day before 5-year milestone anniversary
     */
    public function testOneDayBeforeMilestone(): void
    {
        $hireDate = new DateTime('2019-01-01');
        $calculationDate = new DateTime('2023-12-31'); // 1 day before 5 years

        $result = $this->calculator->calculateAvailableDays($hireDate, 20, $calculationDate);

        // 4 years, 11 months, 30 days = no seniority bonus yet
        // Should get 20 base days (capped at 12 months)
        $this->assertEquals(20, $result);
    }

    // ========================================
    // Carryover Functionality Tests
    // (These tests will FAIL - carryover not implemented yet!)
    // ========================================

    /**
     * Test basic carryover: 3 unused days from previous year
     *
     * Scenario:
     * - Employee gets 20 days annually
     * - Previous year: used 17 days, 3 days unused
     * - Current year: should have 20 (new) + 3 (carried over) = 23 days
     */
    public function testBasicCarryoverThreeUnusedDays(): void
    {
        $hireDate = new DateTime('2020-01-01');
        $calculationDate = new DateTime('2025-01-01');
        $baseDaysPerYear = 20;
        $daysUsedLastYear = 17;
        $carryoverDays = 3; // 20 - 17 = 3 unused

        // This method doesn't exist yet - test will fail!
        $result = $this->calculator->calculateAvailableDays(
            $hireDate,
            $baseDaysPerYear,
            $calculationDate,
            $daysUsedLastYear
        );

        // Expected: 20 (current year) + 3 (carryover) + 5 (seniority bonus for 5 years) = 28 days
        $this->assertEquals(28, $result);
    }

    /**
     * Test edge case: Employee used ALL vacation days last year (no carryover)
     *
     * Scenario:
     * - Employee gets 20 days annually
     * - Previous year: used all 20 days (no unused days)
     * - Current year: should have only 20 new days (no carryover)
     */
    public function testCarryoverWithZeroUnusedDays(): void
    {
        $hireDate = new DateTime('2023-01-01');
        $calculationDate = new DateTime('2025-01-01');
        $baseDaysPerYear = 20;
        $daysUsedLastYear = 20; // Used all days

        // This method doesn't exist yet - test will fail!
        $result = $this->calculator->calculateAvailableDays(
            $hireDate,
            $baseDaysPerYear,
            $calculationDate,
            $daysUsedLastYear
        );

        // Expected: 20 (current year) + 0 (no carryover) = 20 days
        $this->assertEquals(20, $result);
    }
}
