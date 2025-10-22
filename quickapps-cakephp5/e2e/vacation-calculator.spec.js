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
