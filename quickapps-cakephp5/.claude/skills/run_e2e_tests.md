# run_e2e_tests

## Description
Runs End-to-End (E2E) tests using Playwright MCP tools. Tests complete user workflows in a real browser environment.

## Why This Skill Exists

**Problem**: AI often defaults to running Playwright tests via bash commands like:
```bash
npx playwright test
```

**Solution**: This project has Playwright MCP server configured, which provides:
- ✅ Direct browser control from AI
- ✅ Interactive test debugging
- ✅ Screenshot capture on failures
- ✅ Network request monitoring
- ✅ Console error detection
- ✅ Better integration with AI reasoning

**This skill ensures E2E tests are run efficiently with proper analysis.**

## What This Skill Does

1. **Runs Playwright tests** - Via npx or MCP browser automation
2. **Captures failures** - Screenshots and error context
3. **Analyzes results** - What broke and why
4. **Reports clearly** - User-friendly test summaries
5. **Suggests fixes** - Next steps based on failures

## When to Use This Skill

Use `/run_e2e_tests` when you need to:
- Run all E2E tests in the project
- Run tests matching a pattern (--grep)
- Verify UI functionality works end-to-end
- Check for browser console errors
- Test user workflows
- Validate frontend + backend integration

## Arguments

### Run All E2E Tests
```
/run_e2e_tests
```

### Run Tests Matching Pattern
```
/run_e2e_tests --grep "Carryover Feature"
```

### Run Specific Browser
```
/run_e2e_tests --project=chromium
```

### Run in Debug Mode
```
/run_e2e_tests --debug
```

### Combination
```
/run_e2e_tests --grep "Vacation Calculator" --project=firefox
```

## E2E vs Unit Tests

| Aspect | Unit Tests | E2E Tests |
|--------|-----------|-----------|
| **Scope** | Single function | Complete user workflow |
| **Speed** | Fast (milliseconds) | Slower (seconds) |
| **Environment** | Isolated | Real browser |
| **Catches** | Logic bugs | Integration & UI issues |
| **Tool** | PHPUnit MCP | Playwright (bash/MCP) |
| **When** | Every change | Before deploy |

**Both are essential!** Unit tests verify logic, E2E tests verify user experience.

## Test Execution Methods

### Method 1: Bash (Current Default)
For running full test suites:
```bash
npx playwright test --grep "pattern" --project=chromium
```

**Use when:**
- Running multiple tests
- Need full Playwright reporter output
- Want HTML report generation
- Checking overall test suite status

### Method 2: Playwright MCP (Interactive)
For debugging and interactive testing:
```javascript
// Navigate
mcp__playwright__browser_navigate({ url: "http://localhost:8090" })

// Interact
mcp__playwright__browser_click({
  element: "Calculate button",
  ref: "role=button[name='Calculate']"
})

// Verify
mcp__playwright__browser_snapshot()
```

**Use when:**
- Debugging test failures
- Need to inspect page state
- Want to try interactions manually
- Writing new tests

## Expected Behavior

### Step 1: Determine Test Scope
Based on arguments:
- All tests (no arguments)
- Filtered tests (--grep pattern)
- Specific browser (--project)

### Step 2: Execute Tests
Run via bash with proper flags:
```bash
npx playwright test [args] --project=chromium
```

Default to Chromium for consistency unless specified otherwise.

### Step 3: Monitor Execution
Track test progress:
- Tests running
- Passing/failing count
- Execution time
- Timeout warnings

### Step 4: Analyze Results

#### On Success ✅
- Report test counts
- Show execution time
- Confirm all passed

#### On Failure ❌
- List failed tests
- Show error messages
- Identify failure type (timeout, assertion, selector)
- Suggest fixes
- Reference screenshot/trace files

### Step 5: Provide Context
Explain what failed in business terms:
- "Carryover field doesn't exist" (not just "selector not found")
- "Calculation incorrect" (not just "assertion failed")
- "Page didn't load" (not just "timeout")

## Output Format

### Success Output
```
🎭 Running Playwright E2E Tests

Test Suite: Carryover Feature
Browser: Chromium
Filter: --grep "Carryover Feature"

Execution: ✅ Success

Results:
- Total: 6 tests
- Passed: 6 ✅
- Failed: 0 ❌
- Skipped: 0 ⏭️

⏱️ Time: 12.3 seconds

Status: All E2E tests passing! 🎉
```

### Failure Output (RED PHASE - Expected)
```
🎭 Running Playwright E2E Tests

Test Suite: Carryover Feature (RED PHASE)
Browser: Chromium
Filter: --grep "Carryover Feature"

Execution: ❌ Failed (Expected in RED Phase!)

Results:
- Total: 6 tests
- Passed: 1 ✅
- Failed: 5 ❌
- Skipped: 0 ⏭️

Failed Tests:

1️⃣ should display "Days Used Last Year" field in the form
   Error: Locator not found - getByLabel('Days Used Last Year')

   Why: The form field hasn't been implemented yet
   Fix: Add input field to templates/Pages/home.php

   Location: e2e/vacation-calculator.spec.js:245

2️⃣ should calculate carryover when employee has unused vacation days
   Error: Test timeout - waiting for getByLabel('Days Used Last Year')

   Why: Can't fill field that doesn't exist
   Fix: Same as #1 - implement the form field

   Location: e2e/vacation-calculator.spec.js:266

3️⃣ should calculate zero carryover when all vacation days were used
   Error: Test timeout - waiting for getByLabel('Days Used Last Year')

   Why: Same as #1 and #2
   Fix: Implement form field

   Location: e2e/vacation-calculator.spec.js:297

4️⃣ should handle edge case when employee used more days than allocated
   Error: Test timeout - waiting for getByLabel('Days Used Last Year')

   Why: Same root cause
   Fix: Implement form field

   Location: e2e/vacation-calculator.spec.js:327

5️⃣ should display carryover calculation in results breakdown
   Error: Test timeout - waiting for getByLabel('Days Used Last Year')

   Why: Same root cause
   Fix: Implement form field

   Location: e2e/vacation-calculator.spec.js:385

⏱️ Time: 41.5 seconds

Root Cause: Missing UI implementation for carryover feature

Next Steps (GREEN Phase):
1. Add "Days Used Last Year" field to form (templates/Pages/home.php)
2. Update controller to handle parameter (Controller/PagesController.php)
3. Display carryover in results (templates/Pages/home.php)

📊 HTML Report: test-results/index.html
📸 Screenshots: test-results/[test-name]/
```

### Failure Output (Regression - Unexpected)
```
🎭 Running Playwright E2E Tests

Test Suite: All Tests
Browser: Chromium

Execution: ❌ Failed (REGRESSION!)

Results:
- Total: 11 tests
- Passed: 10 ✅
- Failed: 1 ❌
- Skipped: 0 ⏭️

Failed Test:

⚠️ REGRESSION DETECTED - This test was passing before!

1️⃣ should calculate basic vacation days correctly
   Error: Expected "25 days" but got "20 days"

   Why: Recent change broke seniority bonus calculation
   Fix: Check VacationCalculator::calculateAvailableDays()

   Location: e2e/vacation-calculator.spec.js:156

⏱️ Time: 15.8 seconds

🚨 Action Required: Fix regression before proceeding!
```

## Common Test Patterns

### After UI Changes
```
/run_e2e_tests
```
Verify UI works end-to-end.

### During TDD RED PHASE
```
/run_e2e_tests --grep "Carryover Feature"
```
Verify new E2E tests fail as expected (feature not implemented).

### During TDD GREEN PHASE
```
/run_e2e_tests --grep "Carryover"
```
Check if UI implementation makes tests pass.

### Before Committing
```
/run_e2e_tests
```
Ensure no regressions in any user workflows.

## Playwright MCP Tools Available

When debugging or writing tests, these MCP tools are available:

### Navigation
- `mcp__playwright__browser_navigate` - Go to URL
- `mcp__playwright__browser_navigate_back` - Browser back

### Interaction
- `mcp__playwright__browser_click` - Click elements
- `mcp__playwright__browser_type` - Fill text fields
- `mcp__playwright__browser_select_option` - Select dropdowns
- `mcp__playwright__browser_press_key` - Keyboard input
- `mcp__playwright__browser_fill_form` - Fill multiple fields

### Inspection
- `mcp__playwright__browser_snapshot` - Accessibility tree snapshot
- `mcp__playwright__browser_take_screenshot` - Visual screenshot
- `mcp__playwright__browser_console_messages` - Check JS errors
- `mcp__playwright__browser_network_requests` - Monitor API calls

### Waiting
- `mcp__playwright__browser_wait_for` - Wait for text/element

### Control
- `mcp__playwright__browser_close` - Close browser
- `mcp__playwright__browser_tabs` - Manage tabs
- `mcp__playwright__browser_resize` - Change viewport

## Debugging Failed Tests

### Step 1: Identify Failure Type

**Selector Not Found:**
```
Error: Locator not found - getByLabel('Days Used Last Year')
```
→ Element doesn't exist in DOM (UI not implemented)

**Timeout:**
```
Test timeout of 30000ms exceeded
```
→ Element never appeared (async issue or missing element)

**Assertion Failed:**
```
Expected "23 days" but got "20 days"
```
→ Logic error (calculation wrong)

**Network Error:**
```
Failed to fetch http://localhost:8090/calculate
```
→ Backend issue (controller/route problem)

### Step 2: Use MCP to Debug

Navigate to page and inspect:
```
/run_e2e_tests --debug
```

Or manually:
```javascript
mcp__playwright__browser_navigate({ url: "http://localhost:8090" })
mcp__playwright__browser_snapshot()
mcp__playwright__browser_console_messages({ onlyErrors: true })
```

### Step 3: Fix Root Cause

Based on failure type:
- **Selector not found** → Implement UI element
- **Assertion failed** → Fix calculation logic
- **Timeout** → Check async handling
- **Network error** → Fix backend route/controller

## Red Flags to Avoid

I should NEVER:

- ❌ Run tests without analyzing failures
- ❌ Report "tests failed" without explaining WHY
- ❌ Skip running E2E tests because "they're slow"
- ❌ Ignore browser console errors
- ❌ Fix test expectations instead of fixing code
- ❌ Run only unit tests and assume E2E works

## Integration with TDD Workflow

### In RED PHASE (Part 7)
```
/run_e2e_tests --grep "Carryover Feature"
```
**Expected**: Tests FAIL (feature not implemented)
**Result**: 5 failed, 1 passed → Good! RED phase complete ✅

### In GREEN PHASE (Part 8)
```
/run_e2e_tests --grep "Carryover Feature"
```
**Expected**: Tests PASS (feature now implemented)
**Result**: 6 passed, 0 failed → Good! GREEN phase complete ✅

### After Each Change
```
/run_e2e_tests
```
**Expected**: All tests still pass (no regressions)
**Result**: If any fail → Fix before proceeding

## Test Structure

### Test Location
```
quickapps-cakephp5/
├── e2e/
│   ├── vacation-calculator.spec.js
│   └── ... (other test files)
├── playwright.config.js
└── test-results/ (generated)
```

### Test File Structure
```javascript
test.describe('Feature Name', () => {
  test.beforeEach(async ({ page }) => {
    // Setup
  });

  test('should do something', async ({ page }) => {
    // Arrange
    // Act
    // Assert
  });
});
```

## Success Criteria

This skill succeeds when:
- ✅ Tests executed successfully
- ✅ Results clearly reported
- ✅ Failures explained in business terms
- ✅ Root cause identified
- ✅ Next steps provided
- ✅ User understands what to fix

## Troubleshooting

### Tests Timeout
**Cause**: Element not appearing
**Fix**: Check if UI is implemented, use browser_snapshot to debug

### Selector Not Found
**Cause**: Element doesn't exist or wrong selector
**Fix**: Verify element exists, use semantic selectors (getByLabel, getByRole)

### Port Connection Failed
**Cause**: App not running on expected port
**Fix**: Verify `docker-compose up -d` and http://localhost:8090 works

### Flaky Tests
**Cause**: Race conditions or timing issues
**Fix**: Add proper waits, check for element visibility before interaction

## Related Skills

- `run_unit_tests`: Run PHPUnit tests (backend logic)
- `run_red_phase`: Write failing E2E tests for new features
- `run_green_phase`: Implement UI to make E2E tests pass

## Project Context

### Application URL
- Development: http://localhost:8090
- Container: quickapps5-web

### Browsers Supported
- Chromium (default)
- Firefox
- WebKit (Safari)

### Test Reports
- Console output: Real-time during execution
- HTML report: `test-results/index.html`
- Screenshots: `test-results/[test-name]/`
- Traces: Available with `--trace on`

## Notes

**E2E Tests Are Critical**

Unit tests passing ≠ Feature works for users

E2E tests verify:
- ✅ UI renders correctly
- ✅ User can interact with forms
- ✅ Frontend + backend integration works
- ✅ Results display properly
- ✅ No JavaScript errors

**Remember**: E2E tests simulate real users! 🎭👤
