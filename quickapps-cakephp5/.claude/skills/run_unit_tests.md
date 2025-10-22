# run_unit_tests

## Description
Runs PHPUnit tests using MCP tools (NOT bash commands). This is the preferred way to execute tests in this project.

## Why This Skill Exists

**Problem**: AI often defaults to running tests via bash commands like:
```bash
docker exec quickapps5-web vendor/bin/phpunit
```

**Solution**: This project has PHPUnit MCP server configured, which provides:
- ✅ Structured test results (not plain text)
- ✅ Faster execution (no Docker output parsing)
- ✅ Better error analysis
- ✅ Semantic understanding of failures
- ✅ Direct integration with AI tools

**This skill ensures tests are ALWAYS run through MCP PHPUnit tools.**

## What This Skill Does

1. **Uses MCP PHPUnit tools** - Never falls back to bash
2. **Runs appropriate tests** - All tests, specific file, or specific test
3. **Provides clear output** - Structured results, not bash noise
4. **Analyzes failures** - Explains what went wrong
5. **Tracks progress** - Shows test count progression

## When to Use This Skill

Use `/run_unit_tests` when you need to:
- Run all PHPUnit tests in the project
- Run tests for a specific file
- Run a specific test method
- Verify changes didn't break tests
- Check test coverage

**Always prefer this skill over bash commands for running tests!**

## Arguments

### Run All Tests
```
/run_unit_tests
```

### Run Specific Test File
```
/run_unit_tests tests/TestCase/Service/VacationCalculatorTest.php
```

### Run Specific Test Method
```
/run_unit_tests --filter testBasicVacationCarryover
```

### Run Tests for a Class
```
/run_unit_tests VacationCalculatorTest
```

## MCP Tools Used

This skill uses these MCP PHPUnit tools:

### 1. `mcp__phpunit__run_tests`
Runs all tests or tests in a specific path.

**Usage:**
```javascript
// Run all tests
mcp__phpunit__run_tests()

// Run specific file (IMPORTANT: use relative path, not absolute!)
mcp__phpunit__run_tests({
  path: "tests/TestCase/Service/VacationCalculatorTest.php"
})
```

**⚠️ CRITICAL PATH RULE:**
- ✅ Use relative paths: `tests/TestCase/Service/VacationCalculatorTest.php`
- ❌ Never use absolute paths: `/Users/alex/.../tests/...`
- ❌ Never use container paths: `/var/www/html/tests/...`

The MCP server runs inside Docker container and paths are relative to `/var/www/html`.

### 2. `mcp__phpunit__run_specific_test`
Runs tests matching a filter pattern.

**Usage:**
```javascript
// Run specific test method
mcp__phpunit__run_specific_test({
  filter: "testBasicVacationCarryover"
})

// Run all tests in a class
mcp__phpunit__run_specific_test({
  filter: "VacationCalculatorTest"
})
```

### 3. `mcp__phpunit__list_tests`
Lists all available tests without running them.

**Usage:**
```javascript
// List all tests
mcp__phpunit__list_tests()

// List tests in specific path
mcp__phpunit__list_tests({
  path: "tests/TestCase/Service"
})
```

### 4. `mcp__phpunit__get_configuration`
Gets PHPUnit version and configuration info.

**Usage:**
```javascript
mcp__phpunit__get_configuration()
```

## Expected Behavior

### Step 1: Determine Test Scope
Based on arguments, decide:
- All tests (no arguments)
- Specific file (path provided)
- Specific test (filter provided)

### Step 2: Execute via MCP
Use appropriate MCP tool:
- `mcp__phpunit__run_tests` for all/file
- `mcp__phpunit__run_specific_test` for filters

**Never use bash commands!**

### Step 3: Analyze Results
Parse structured MCP output:
- Test count (total, passed, failed)
- Execution time
- Failure details (if any)
- Assertions count

### Step 4: Report Results
Provide clear summary:
```
✅ All 23 tests passed (25 assertions)
⏱️ Execution time: 1.23s
```

Or if failures:
```
❌ 2 tests failed, 21 passed

Failed tests:
1. testBasicVacationCarryover
   - Expected: 23
   - Actual: 20
   - Reason: Carryover parameter not implemented

2. testZeroCarryover
   - Expected: 20
   - Actual: 20
   - Reason: Method doesn't accept 4th parameter
```

## Common Patterns

### After Code Changes
```
/run_unit_tests
```
Verify no regressions.

### During TDD RED PHASE
```
/run_unit_tests tests/TestCase/Service/VacationCalculatorTest.php
```
Verify new tests fail as expected.

### During TDD GREEN PHASE
```
/run_unit_tests --filter testBasicVacationCarryover
```
Check if specific test now passes.

### After Refactoring
```
/run_unit_tests
```
Ensure all tests still pass.

## Output Format

### Success Output
```
🧪 Running PHPUnit Tests via MCP

Test Suite: All Tests
Execution: ✅ Success

Results:
- Total: 23 tests
- Passed: 23 ✅
- Failed: 0 ❌
- Assertions: 25

⏱️ Time: 1.23 seconds

Status: All tests passing! 🎉
```

### Failure Output
```
🧪 Running PHPUnit Tests via MCP

Test Suite: All Tests
Execution: ❌ Failed

Results:
- Total: 23 tests
- Passed: 21 ✅
- Failed: 2 ❌
- Assertions: 25

Failed Tests:

1️⃣ VacationCalculatorTest::testBasicVacationCarryover
   Location: tests/TestCase/Service/VacationCalculatorTest.php:123

   Error: Too few arguments to function calculateAvailableDays(), 3 passed but 4 expected

   Expected: Function should accept 4th parameter for carryover days
   Actual: Function only accepts 3 parameters

   Fix needed: Add optional 4th parameter $carriedOverDays = 0

2️⃣ VacationCalculatorTest::testZeroCarryover
   Location: tests/TestCase/Service/VacationCalculatorTest.php:145

   (same issue as above)

⏱️ Time: 1.45 seconds

Next steps: Implement carryover parameter in calculateAvailableDays()
```

## Red Flags to Avoid

I should NEVER:

- ❌ Use bash commands (`docker exec ... phpunit`)
- ❌ Parse plain text output from bash
- ❌ Use absolute paths in MCP calls
- ❌ Fall back to bash if MCP "seems slow"
- ❌ Run tests without reporting results

## Integration with TDD Workflow

### In RED PHASE
```
/run_unit_tests tests/TestCase/Service/VacationCalculatorTest.php
```
Verify new tests FAIL (this is success in RED phase!).

### In GREEN PHASE
```
/run_unit_tests --filter testCarryover
```
Iteratively check if implementation makes tests pass.

### In REFACTOR PHASE
```
/run_unit_tests
```
After each refactoring, ensure tests stay green.

## Troubleshooting

### If MCP PHPUnit is not configured:
1. Check `.mcp.json` or `.claude/mcp/config.json`
2. Verify Docker container is running: `docker ps | grep quickapps5-web`
3. Test MCP connection: `/mcp` command
4. If still broken, ask user to configure MCP server

### If tests fail to run:
1. Check if container is running
2. Verify PHPUnit is installed: `mcp__phpunit__get_configuration()`
3. Check path is relative (not absolute)

## Project Context

### Docker Environment
- Container: `quickapps5-web`
- Working directory: `/var/www/html`
- PHPUnit binary: `vendor/bin/phpunit`

### MCP Server Configuration
The MCP server runs inside Docker:
```bash
docker exec -i quickapps5-web php vendor/bin/mcp-phpunit-server
```

All paths are relative to container's `/var/www/html`.

### Test Structure
```
src/
├── tests/
│   ├── TestCase/
│   │   ├── Controller/
│   │   ├── Service/
│   │   │   └── VacationCalculatorTest.php
│   │   └── ...
│   └── Fixture/
```

## Related Skills

- `run_red_phase`: Write failing tests (uses this skill to verify they fail)
- `run_green_phase`: Implement code (uses this skill to verify tests pass)
- `run_refactor_phase`: Improve code (uses this skill to avoid regressions)

## Success Criteria

This skill succeeds when:
- ✅ Tests executed via MCP (not bash)
- ✅ Structured results provided
- ✅ Clear analysis of failures
- ✅ User understands test status
- ✅ Next steps are obvious

## Notes

**Why MCP over Bash?**

1. **Structured output**: JSON vs plain text
2. **Faster**: No Docker exec overhead parsing
3. **Better errors**: Semantic understanding of failures
4. **Integration**: AI can reason about test results better
5. **Workshop goal**: Learn to use MCP tools effectively

**Remember**: Always use MCP PHPUnit tools, never bash commands! 🧪
