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
}
