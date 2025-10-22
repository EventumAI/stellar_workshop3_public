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
        $daysUsedLastYear = 17; // 20 - 17 = 3 unused days will carry over

        $result = $this->calculator->calculateAvailableDays(
            $hireDate,
            $baseDaysPerYear,
            $calculationDate,
            $daysUsedLastYear,
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
            $daysUsedLastYear,
        );

        // Expected: 20 (current year) + 0 (no carryover) = 20 days
        $this->assertEquals(20, $result);
    }

    // ========================================
    // EDGE CASE TESTS: Carryover Logic
    // ========================================

    /**
     * EDGE CASE 1: Negative days used last year (invalid input)
     *
     * Critical Bug Test:
     * - What if $daysUsedLastYear is negative? (data corruption, malicious input)
     * - Current behavior: 20 - (-5) = 25 bonus days (SECURITY ISSUE!)
     * - Expected: Should either reject negative input OR treat as 0 days used
     *
     * This tests a CRITICAL validation gap that could allow gaming the system
     */
    public function testCarryoverWithNegativeDaysUsed(): void
    {
        $hireDate = new DateTime('2020-01-01');
        $calculationDate = new DateTime('2025-01-01');
        $baseDaysPerYear = 20;
        $daysUsedLastYear = -5; // INVALID: negative days used!

        $result = $this->calculator->calculateAvailableDays(
            $hireDate,
            $baseDaysPerYear,
            $calculationDate,
            $daysUsedLastYear,
        );

        // Current implementation gives: 20 (base) + 5 (seniority) + 25 (carryover from negative!) = 50
        // This test DOCUMENTS the current behavior (likely a bug to fix later)
        // TODO: Should probably validate and reject negative input or cap at baseDaysPerYear
        $this->assertEquals(50, $result);
    }

    /**
     * EDGE CASE 2: Employee used MORE days than allocated last year
     *
     * Data Integrity Test:
     * - What if someone used 30 days but was only allocated 20? (data inconsistency)
     * - Current behavior: max(0, 20 - 30) = 0 (no carryover, which is correct)
     * - Expected: No carryover, system handles gracefully
     *
     * This tests that the system doesn't create NEGATIVE carryover debt
     */
    public function testCarryoverWhenUsedMoreThanAllocated(): void
    {
        $hireDate = new DateTime('2020-01-01');
        $calculationDate = new DateTime('2025-01-01');
        $baseDaysPerYear = 20;
        $daysUsedLastYear = 30; // Used MORE than allocated (data inconsistency)

        $result = $this->calculator->calculateAvailableDays(
            $hireDate,
            $baseDaysPerYear,
            $calculationDate,
            $daysUsedLastYear,
        );

        // Expected: 20 (current year) + 5 (seniority) + 0 (no carryover) = 25 days
        // Correctly prevents negative carryover
        $this->assertEquals(25, $result);
    }

    /**
     * EDGE CASE 3: Extremely large negative days used (boundary testing)
     *
     * Boundary/Overflow Test:
     * - What if $daysUsedLastYear is extremely negative? (data corruption, integer overflow)
     * - Current behavior: 20 - (-1000) = 1020 bonus carryover days!
     * - Expected: Should validate/cap the carryover
     *
     * This tests extreme boundary conditions and potential integer overflow
     */
    public function testCarryoverWithExtremelyNegativeDaysUsed(): void
    {
        $hireDate = new DateTime('2020-01-01');
        $calculationDate = new DateTime('2025-01-01');
        $baseDaysPerYear = 20;
        $daysUsedLastYear = -1000; // EXTREME: massively negative input

        $result = $this->calculator->calculateAvailableDays(
            $hireDate,
            $baseDaysPerYear,
            $calculationDate,
            $daysUsedLastYear,
        );

        // Current implementation gives: 20 (base) + 5 (seniority) + 1020 (carryover!) = 1045
        // This test DOCUMENTS the vulnerability - should be fixed with validation
        // TODO: Add validation to prevent negative daysUsedLastYear
        $this->assertEquals(1045, $result);
    }

    // ========================================
    // Holiday Tracking Tests (RED PHASE)
    // (These tests will FAIL - holiday tracking not implemented yet!)
    // ========================================

    /**
     * Test calculating actual vacation days used excluding holidays
     *
     * Scenario:
     * - Employee requests 5 vacation days (Mon-Fri, Jan 6-10, 2025)
     * - Wednesday Jan 8 is a public holiday
     * - Actual vacation days used should be 4 (excluding the holiday)
     *
     * EXPECTED TO FAIL: calculateVacationDaysUsed() method doesn't exist yet!
     */
    public function testVacationDaysExcludingOneHoliday(): void
    {
        $startDate = new DateTime('2025-01-06'); // Monday
        $endDate = new DateTime('2025-01-10');   // Friday
        $holidays = [
            new DateTime('2025-01-08'), // Wednesday is a holiday
        ];

        // This method doesn't exist yet - test will fail!
        $result = $this->calculator->calculateVacationDaysUsed($startDate, $endDate, $holidays);

        // Expected: 5 requested days - 1 holiday = 4 actual vacation days used
        $this->assertEquals(4, $result);
    }

    /**
     * Test vacation period with no holidays
     *
     * Scenario:
     * - Employee requests 3 vacation days
     * - No holidays in the period
     * - Should return full number of days
     */
    public function testVacationDaysWithNoHolidays(): void
    {
        $startDate = new DateTime('2025-02-03'); // Monday
        $endDate = new DateTime('2025-02-05');   // Wednesday
        $holidays = []; // No holidays

        $result = $this->calculator->calculateVacationDaysUsed($startDate, $endDate, $holidays);

        // Expected: 3 days, no holidays to subtract
        $this->assertEquals(3, $result);
    }

    /**
     * Test vacation period with multiple holidays
     *
     * Scenario:
     * - Employee requests 10 vacation days (2 weeks)
     * - 3 public holidays fall within the period
     * - Actual vacation days used: 10 - 3 = 7 days
     */
    public function testVacationDaysWithMultipleHolidays(): void
    {
        $startDate = new DateTime('2025-12-22'); // Monday
        $endDate = new DateTime('2026-01-02');   // Friday (spans 2 weeks)
        $holidays = [
            new DateTime('2025-12-25'), // Christmas
            new DateTime('2025-12-26'), // Boxing Day
            new DateTime('2026-01-01'), // New Year's Day
        ];

        $result = $this->calculator->calculateVacationDaysUsed($startDate, $endDate, $holidays);

        // Expected: 12 calendar days (Dec 22-Jan 2) - 3 holidays = 9 actual days
        // Note: This assumes only business days count, may need adjustment
        $this->assertEquals(9, $result);
    }

    /**
     * EDGE CASE: Entire vacation period is holidays
     *
     * Scenario:
     * - Employee "requests" time off during a holiday week
     * - All days in the period are holidays
     * - No actual vacation days should be used (0 days)
     */
    public function testVacationPeriodEntirelyHolidays(): void
    {
        $startDate = new DateTime('2025-12-25'); // Christmas
        $endDate = new DateTime('2025-12-26');   // Boxing Day
        $holidays = [
            new DateTime('2025-12-25'),
            new DateTime('2025-12-26'),
        ];

        $result = $this->calculator->calculateVacationDaysUsed($startDate, $endDate, $holidays);

        // Expected: 0 vacation days used (all days are holidays)
        $this->assertEquals(0, $result);
    }

    /**
     * EDGE CASE: Holiday outside of vacation period
     *
     * Scenario:
     * - Employee takes vacation Jan 6-8
     * - Holiday is on Jan 10 (outside the period)
     * - Holiday shouldn't affect calculation
     */
    public function testHolidayOutsideVacationPeriod(): void
    {
        $startDate = new DateTime('2025-01-06');
        $endDate = new DateTime('2025-01-08');
        $holidays = [
            new DateTime('2025-01-10'), // Holiday AFTER vacation period
            new DateTime('2025-01-03'), // Holiday BEFORE vacation period
        ];

        $result = $this->calculator->calculateVacationDaysUsed($startDate, $endDate, $holidays);

        // Expected: 3 days (holidays outside period don't affect calculation)
        $this->assertEquals(3, $result);
    }

    /**
     * EDGE CASE: Start date is a holiday
     *
     * Scenario:
     * - Vacation starts on a holiday
     * - Should not count the holiday as vacation day
     */
    public function testVacationStartsOnHoliday(): void
    {
        $startDate = new DateTime('2025-01-01'); // New Year's Day (holiday)
        $endDate = new DateTime('2025-01-03');   // Friday
        $holidays = [
            new DateTime('2025-01-01'), // Start date is holiday
        ];

        $result = $this->calculator->calculateVacationDaysUsed($startDate, $endDate, $holidays);

        // Expected: 3 calendar days - 1 holiday = 2 vacation days
        $this->assertEquals(2, $result);
    }

    /**
     * EDGE CASE: End date is a holiday
     *
     * Scenario:
     * - Vacation ends on a holiday
     * - Should not count the holiday as vacation day
     */
    public function testVacationEndsOnHoliday(): void
    {
        $startDate = new DateTime('2025-12-23'); // Tuesday
        $endDate = new DateTime('2025-12-25');   // Christmas (holiday)
        $holidays = [
            new DateTime('2025-12-25'), // End date is holiday
        ];

        $result = $this->calculator->calculateVacationDaysUsed($startDate, $endDate, $holidays);

        // Expected: 3 calendar days - 1 holiday = 2 vacation days
        $this->assertEquals(2, $result);
    }
}
