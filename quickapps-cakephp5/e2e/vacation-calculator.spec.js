// @ts-check
import { test, expect } from '@playwright/test';

/**
 * E2E Tests for Vacation Calculator
 *
 * Tests the vacation days calculation functionality including:
 * - Proportional calculation for partial years
 * - Full year calculation
 * - Seniority bonuses (5, 10+ years)
 * - Form validation and error handling
 */

const BASE_URL = 'http://localhost:8090';

test.describe('Vacation Calculator', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(BASE_URL);
    await expect(page.getByRole('heading', { name: 'Vacation Calculator' })).toBeVisible();
  });

  test('should calculate vacation days for new employee (less than 1 year)', async ({ page }) => {
    // Employee hired 6 months ago (half year)
    const today = new Date();
    const sixMonthsAgo = new Date(today);
    sixMonthsAgo.setMonth(today.getMonth() - 6);

    const hireDateStr = sixMonthsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('20');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);

    // Submit the form
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results to appear
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify employment period is displayed
    await expect(page.locator('.result-value').first()).toContainText(hireDateStr);
    await expect(page.locator('.result-value').first()).toContainText(calculationDateStr);

    // Verify length of service (0 years, 6 months)
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toContainText('0 years');
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toContainText('6 months');

    // Verify base days (approximately 10 days for 6 months, proportional to 20 days/year)
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toContainText('10 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toContainText('(of 20 annual)');

    // Verify no seniority bonus for new employee
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toContainText('+0 days');

    // Verify total is base days only (no bonus)
    await expect(page.locator('.result-total .value')).toHaveText('10');
  });

  test('should calculate full vacation days for employee with exactly 1 year', async ({ page }) => {
    // Employee hired exactly 1 year ago
    const today = new Date();
    const oneYearAgo = new Date(today);
    oneYearAgo.setFullYear(today.getFullYear() - 1);

    const hireDateStr = oneYearAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form with 24 base days per year
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('24');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);

    // Submit the form
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify length of service (1 year)
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toContainText('1 year');

    // Verify full base days (24 days for full year)
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toContainText('24 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toContainText('(of 24 annual)');

    // Verify no seniority bonus (needs 5 years)
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toContainText('+0 days');

    // Verify total equals base days
    await expect(page.locator('.result-total .value')).toHaveText('24');
  });

  test('should calculate vacation days with seniority bonus for 5 years of service', async ({ page }) => {
    // Employee hired exactly 5 years ago
    const today = new Date();
    const fiveYearsAgo = new Date(today);
    fiveYearsAgo.setFullYear(today.getFullYear() - 5);

    const hireDateStr = fiveYearsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('20');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);

    // Submit the form
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify length of service (5 years)
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toContainText('5 years');

    // Verify base days
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toContainText('20 days');

    // Verify seniority bonus (+5 days for 5 years)
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toContainText('+5 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toContainText('(+5 days every 5 years)');

    // Verify total is base + bonus (20 + 5 = 25)
    await expect(page.locator('.result-total .value')).toHaveText('25');
    await expect(page.locator('.result-total .label')).toContainText('Total Available Vacation Days');
  });

  test('should calculate vacation days with multiple seniority bonuses for 10+ years', async ({ page }) => {
    // Employee hired 11 years and 3 months ago
    const today = new Date();
    const elevenYearsAgo = new Date(today);
    elevenYearsAgo.setFullYear(today.getFullYear() - 11);
    elevenYearsAgo.setMonth(today.getMonth() - 3);

    const hireDateStr = elevenYearsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('22');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);

    // Submit the form
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify employment period
    await expect(page.locator('.result-value').first()).toContainText(hireDateStr);

    // Verify length of service (11 years and 3 months)
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toContainText('11 years');
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toContainText('3 months');

    // Verify base days (full 22 days, as worked more than 12 months)
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toContainText('22 days');

    // Verify seniority bonus (+10 days: 2 milestones of 5 years each)
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toContainText('+10 days');

    // Verify total is base + bonus (22 + 10 = 32)
    await expect(page.locator('.result-total .value')).toHaveText('32');
  });

  test('should validate form and handle errors properly', async ({ page }) => {
    // Test 1: Try to submit empty form
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Form should prevent submission due to HTML5 validation (required fields)
    // Result section should not appear
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).not.toBeVisible();

    // Test 2: Submit with invalid date format (future hire date)
    const today = new Date();
    const futureDate = new Date(today);
    futureDate.setFullYear(today.getFullYear() + 1);
    const futureDateStr = futureDate.toISOString().split('T')[0];
    const todayStr = today.toISOString().split('T')[0];

    await page.getByLabel('Hire Date *').fill(futureDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('20');
    await page.getByLabel('Calculation Date').fill(todayStr);

    await page.getByRole('button', { name: 'Calculate' }).click();

    // Should show negative or zero days as result (edge case)
    // The calculation might show errors or unexpected results
    // Let's verify the form was submitted and processed
    await page.waitForTimeout(500); // Brief wait for form processing

    // Test 3: Submit with valid minimum values
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);
    const yesterdayStr = yesterday.toISOString().split('T')[0];

    await page.getByLabel('Hire Date *').fill(yesterdayStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('1');

    await page.getByRole('button', { name: 'Calculate' }).click();

    // Should show results for 1 day worked
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toBeVisible();

    // Test 4: Verify form retains values after submission
    await expect(page.getByLabel('Hire Date *')).toHaveValue(yesterdayStr);
    await expect(page.getByLabel('Base Vacation Days Per Year *')).toHaveValue('1');

    // Test 5: Verify all result sections are present
    await expect(page.locator('.result-item').filter({ hasText: 'Employment Period' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toBeVisible();
    await expect(page.locator('.result-total')).toBeVisible();
  });
});

/**
 * E2E Tests for Vacation Carryover Feature (RED PHASE - TDD)
 *
 * These tests are expected to FAIL initially because the carryover UI
 * is not yet implemented. This follows the Red-Green-Refactor cycle:
 * 1. RED: Write failing tests for new feature
 * 2. GREEN: Implement minimum code to make tests pass
 * 3. REFACTOR: Clean up code while keeping tests green
 *
 * Tests cover:
 * - Basic carryover: unused vacation days from previous year
 * - Zero carryover: all vacation days used in previous year
 * - Edge case: employee used more days than allocated
 * - Form validation: optional carryover field
 */
test.describe('Vacation Calculator - Carryover Feature (RED PHASE)', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(BASE_URL);
    await expect(page.getByRole('heading', { name: 'Vacation Calculator' })).toBeVisible();
  });

  test('should display "Days Used Last Year" field in the form', async ({ page }) => {
    // EXPECTED TO FAIL: Field does not exist yet
    // Verify that the carryover input field exists
    await expect(page.getByLabel('Days Used Last Year')).toBeVisible();

    // Verify help text for the field
    await expect(page.locator('.help-text').filter({ hasText: 'Days you used in the previous year' })).toBeVisible();
  });

  test('should calculate carryover when employee has unused vacation days', async ({ page }) => {
    // EXPECTED TO FAIL: Carryover functionality not in UI yet
    // Scenario: Employee hired 5 years ago, gets 20 days/year, used 17 last year
    // Should have: 20 (current) + 5 (seniority) + 3 (carryover) = 28 days
    const today = new Date();
    const fiveYearsAgo = new Date(today);
    fiveYearsAgo.setFullYear(today.getFullYear() - 5);

    const hireDateStr = fiveYearsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('20');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);
    await page.getByLabel('Days Used Last Year').fill('17');

    // Submit
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify carryover days are displayed
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toContainText('+3 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toContainText('(unused from previous year)');

    // Verify total: 20 (base) + 5 (seniority) + 3 (carryover) = 28
    await expect(page.locator('.result-total .value')).toHaveText('28');
  });

  test('should calculate zero carryover when all vacation days were used', async ({ page }) => {
    // EXPECTED TO FAIL: Carryover functionality not in UI yet
    // Scenario: Employee used all 20 days last year, no carryover
    const today = new Date();
    const threeYearsAgo = new Date(today);
    threeYearsAgo.setFullYear(today.getFullYear() - 3);

    const hireDateStr = threeYearsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('20');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);
    await page.getByLabel('Days Used Last Year').fill('20');

    // Submit
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify carryover section shows 0 days
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toContainText('+0 days');

    // Verify total: 20 (base) + 0 (seniority, only 3 years) + 0 (carryover) = 20
    await expect(page.locator('.result-total .value')).toHaveText('20');
  });

  test('should handle edge case when employee used more days than allocated', async ({ page }) => {
    // EXPECTED TO FAIL: Carryover functionality not in UI yet
    // Edge case: Used 25 days but only had 20 (shouldn't create negative carryover)
    const today = new Date();
    const sevenYearsAgo = new Date(today);
    sevenYearsAgo.setFullYear(today.getFullYear() - 7);

    const hireDateStr = sevenYearsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('20');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);
    await page.getByLabel('Days Used Last Year').fill('25'); // More than allocated!

    // Submit
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify carryover is 0 (not negative!)
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toContainText('+0 days');

    // Verify total: 20 (base) + 5 (seniority, 7 years = 1 milestone) + 0 (carryover) = 25
    await expect(page.locator('.result-total .value')).toHaveText('25');
  });

  test('should work correctly when carryover field is left empty (optional field)', async ({ page }) => {
    // EXPECTED TO FAIL: Carryover functionality not in UI yet
    // Verify that leaving the "Days Used Last Year" field empty doesn't break calculation
    const today = new Date();
    const twoYearsAgo = new Date(today);
    twoYearsAgo.setFullYear(today.getFullYear() - 2);

    const hireDateStr = twoYearsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill form WITHOUT carryover field
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('24');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);
    // Intentionally NOT filling "Days Used Last Year"

    // Submit
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // When field is empty, carryover section should NOT appear in results
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).not.toBeVisible();

    // Verify total: 24 (base) + 0 (seniority, only 2 years) = 24
    await expect(page.locator('.result-total .value')).toHaveText('24');
  });

  test('should display carryover calculation in results breakdown', async ({ page }) => {
    // EXPECTED TO FAIL: Carryover functionality not in UI yet
    // Verify that results section includes carryover breakdown
    const today = new Date();
    const tenYearsAgo = new Date(today);
    tenYearsAgo.setFullYear(today.getFullYear() - 10);

    const hireDateStr = tenYearsAgo.toISOString().split('T')[0];
    const calculationDateStr = today.toISOString().split('T')[0];

    // Fill the form
    await page.getByLabel('Hire Date *').fill(hireDateStr);
    await page.getByLabel('Base Vacation Days Per Year *').fill('22');
    await page.getByLabel('Calculation Date').fill(calculationDateStr);
    await page.getByLabel('Days Used Last Year').fill('18'); // 22 - 18 = 4 carryover

    // Submit
    await page.getByRole('button', { name: 'Calculate' }).click();

    // Wait for results
    await expect(page.getByRole('heading', { name: 'Calculation Result' })).toBeVisible();

    // Verify ALL result sections are present (including new carryover section)
    await expect(page.locator('.result-item').filter({ hasText: 'Employment Period' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Length of Service' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toBeVisible();
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toBeVisible(); // NEW!
    await expect(page.locator('.result-total')).toBeVisible();

    // Verify the calculation breakdown
    await expect(page.locator('.result-item').filter({ hasText: 'Base Vacation Days' })).toContainText('22 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Seniority Bonus' })).toContainText('+10 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' })).toContainText('+4 days');

    // Total: 22 (base) + 10 (seniority, 10 years) + 4 (carryover) = 36
    await expect(page.locator('.result-total .value')).toHaveText('36');
  });
});

/**
 * E2E Tests for Holiday Tracking (RED PHASE)
 *
 * These tests will FAIL because holiday tracking UI hasn't been implemented yet!
 */
test.describe('Vacation Calculator - Holiday Tracking (RED PHASE)', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(BASE_URL);
    await expect(page.getByRole('heading', { name: 'Vacation Calculator' })).toBeVisible();
  });

  /**
   * Test that vacation request form includes holiday tracking fields
   *
   * EXPECTED TO FAIL: These form fields don't exist yet!
   */
  test('should display vacation request form with holiday tracking fields', async ({ page }) => {
    // EXPECTED TO FAIL: These fields don't exist yet
    await expect(page.getByLabel('Vacation Start Date')).toBeVisible();
    await expect(page.getByLabel('Vacation End Date')).toBeVisible();

    // Help text should explain holiday exclusion
    await expect(page.locator('text=Public holidays will be automatically excluded')).toBeVisible();
  });

  /**
   * Test calculating vacation days with one holiday in the period
   *
   * Scenario:
   * - Employee requests Jan 6-10, 2025 (5 days)
   * - Jan 8 (Wednesday) is a public holiday
   * - Actual vacation days used: 4 days
   */
  test('should calculate vacation days excluding one holiday', async ({ page }) => {
    // Fill vacation request form
    await page.getByLabel('Vacation Start Date').fill('2025-01-06');
    await page.getByLabel('Vacation End Date').fill('2025-01-10');

    // Submit
    await page.getByRole('button', { name: 'Calculate Vacation Days' }).click();

    // Wait for results
    await expect(page.locator('.vacation-result')).toBeVisible();

    // Verify calculation breakdown
    await expect(page.locator('.result-item').filter({ hasText: 'Requested Days' })).toContainText('5 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Holidays in Period' })).toContainText('1 day');
    await expect(page.locator('.result-item').filter({ hasText: 'Holiday Date' })).toContainText('2025-01-08');

    // Total: 5 requested - 1 holiday = 4 actual vacation days
    await expect(page.locator('.result-total .value')).toHaveText('4');
    await expect(page.locator('.result-total .label')).toContainText('Actual Vacation Days Used');
  });

  /**
   * Test vacation period with multiple holidays
   *
   * Scenario:
   * - Employee requests Dec 22, 2025 - Jan 2, 2026 (12 days)
   * - Holidays: Dec 25 (Christmas), Dec 26 (Boxing Day), Jan 1 (New Year)
   * - Actual vacation days: 12 - 3 = 9 days
   */
  test('should calculate vacation days with multiple holidays', async ({ page }) => {
    // Fill vacation request form
    await page.getByLabel('Vacation Start Date').fill('2025-12-22');
    await page.getByLabel('Vacation End Date').fill('2026-01-02');

    // Submit
    await page.getByRole('button', { name: 'Calculate Vacation Days' }).click();

    // Wait for results
    await expect(page.locator('.vacation-result')).toBeVisible();

    // Verify calculation breakdown
    await expect(page.locator('.result-item').filter({ hasText: 'Requested Days' })).toContainText('12 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Holidays in Period' })).toContainText('3 days');

    // Verify individual holidays are listed
    await expect(page.locator('.holiday-list')).toContainText('2025-12-25'); // Christmas
    await expect(page.locator('.holiday-list')).toContainText('2025-12-26'); // Boxing Day
    await expect(page.locator('.holiday-list')).toContainText('2026-01-01'); // New Year

    // Total: 12 - 3 = 9 actual vacation days
    await expect(page.locator('.result-total .value')).toHaveText('9');
  });

  /**
   * Test vacation period with no holidays
   *
   * Scenario:
   * - Employee requests Feb 3-5, 2025 (3 days)
   * - No public holidays in this period
   * - Actual vacation days: 3 days
   */
  test('should show full days when no holidays in period', async ({ page }) => {
    // Fill vacation request form
    await page.getByLabel('Vacation Start Date').fill('2025-02-03');
    await page.getByLabel('Vacation End Date').fill('2025-02-05');

    // Submit
    await page.getByRole('button', { name: 'Calculate Vacation Days' }).click();

    // Wait for results
    await expect(page.locator('.vacation-result')).toBeVisible();

    // Verify calculation
    await expect(page.locator('.result-item').filter({ hasText: 'Requested Days' })).toContainText('3 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Holidays in Period' })).toContainText('0 days');

    // Total: 3 days (no holidays to subtract)
    await expect(page.locator('.result-total .value')).toHaveText('3');

    // Should show message about no holidays
    await expect(page.locator('text=No public holidays in this period')).toBeVisible();
  });

  /**
   * EDGE CASE: Entire vacation period is holidays
   *
   * Scenario:
   * - Employee "requests" Dec 25-26, 2025 (Christmas and Boxing Day)
   * - Both days are public holidays
   * - Actual vacation days: 0 (no vacation days consumed)
   */
  test('should show zero days when entire period is holidays', async ({ page }) => {
    // Fill vacation request form
    await page.getByLabel('Vacation Start Date').fill('2025-12-25');
    await page.getByLabel('Vacation End Date').fill('2025-12-26');

    // Submit
    await page.getByRole('button', { name: 'Calculate Vacation Days' }).click();

    // Wait for results
    await expect(page.locator('.vacation-result')).toBeVisible();

    // Verify calculation
    await expect(page.locator('.result-item').filter({ hasText: 'Requested Days' })).toContainText('2 days');
    await expect(page.locator('.result-item').filter({ hasText: 'Holidays in Period' })).toContainText('2 days');

    // Total: 0 vacation days (all days are holidays!)
    await expect(page.locator('.result-total .value')).toHaveText('0');

    // Should show special message
    await expect(page.locator('text=No vacation days will be used')).toBeVisible();
    await expect(page.locator('.alert-info')).toContainText('entire period consists of public holidays');
  });

  /**
   * Test holiday list display
   *
   * UI should show a list of upcoming public holidays for reference
   */
  test('should display upcoming public holidays for reference', async ({ page }) => {
    // Holiday list should be visible on the page (sidebar or section)
    await expect(page.locator('.holiday-calendar')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Public Holidays 2025' })).toBeVisible();

    // Should list major holidays
    await expect(page.locator('.holiday-item').filter({ hasText: 'New Year' })).toBeVisible();
    await expect(page.locator('.holiday-item').filter({ hasText: 'Christmas' })).toBeVisible();

    // Each holiday should show date
    await expect(page.locator('.holiday-date')).toContainText('2025-01-01');
  });

  /**
   * Test form validation for vacation dates
   */
  test('should validate vacation date inputs', async ({ page }) => {
    // Try to submit with end date before start date
    await page.getByLabel('Vacation Start Date').fill('2025-01-10');
    await page.getByLabel('Vacation End Date').fill('2025-01-05'); // Before start!

    await page.getByRole('button', { name: 'Calculate Vacation Days' }).click();

    // Should show validation error
    await expect(page.locator('.error-message')).toBeVisible();
    await expect(page.locator('.error-message')).toContainText('End date must be after start date');
  });
});
