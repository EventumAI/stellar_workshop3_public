# run_red_phase

## Description
Executes the RED PHASE of Test-Driven Development (TDD) - writing failing tests for functionality that doesn't exist yet.

## What is RED PHASE?

The RED PHASE is the first step in the TDD cycle (Red → Green → Refactor):

1. 🔴 **RED**: Write tests for new functionality BEFORE implementing it
2. 🟢 **GREEN**: Write minimal code to make tests pass
3. 🔵 **REFACTOR**: Improve code while keeping tests green

### RED PHASE Philosophy

> **Write tests that FAIL because the feature doesn't exist yet**

The RED PHASE is about:
- ✅ Understanding requirements deeply
- ✅ Designing the API/interface through tests
- ✅ Creating a roadmap for implementation
- ✅ Documenting expected behavior
- ❌ NOT implementing the feature (that's GREEN PHASE)
- ❌ NOT making tests pass (they should FAIL)

## What This Skill Does

This skill will:

1. **Analyze requirements**: Understand the feature to be built
2. **Design tests**: Write clear, focused tests for the feature
3. **Run tests**: Execute tests to verify they FAIL as expected
4. **Document failures**: Show what needs to be implemented
5. **Explain next steps**: What GREEN PHASE should do

## When to Use This Skill

Use `run_red_phase` when you want to:
- Start building a new feature using TDD
- Add new functionality to existing code
- Explore requirements through test-first design
- Document expected behavior before coding

## Arguments

The skill expects a feature description after the command:

```
/run_red_phase Add vacation carryover from previous year
```

Or provide detailed requirements:

```
/run_red_phase Feature: Employee vacation carryover
Requirements:
- Unused days from previous year should carry over
- Calculation: (base days) - (days used last year) = carryover
- Carryover adds to current year's allocation
- Should be optional (can be skipped)
```

## Expected Behavior

### Step 1: Requirements Analysis
- Read and understand the feature requirements
- Identify edge cases and boundary conditions
- Ask clarifying questions if requirements are ambiguous

### Step 2: Test Design
- Write 2-5 focused tests for the feature
- Include at least one edge case test
- Use clear, descriptive test names
- Add documentation explaining what each test verifies

### Step 3: Test Execution
- **Use `/run_unit_tests` for PHPUnit tests** (automatically uses MCP)
- **Use `/run_e2e_tests` for Playwright tests** (proper test execution)
- **Verify tests FAIL** (this is success in RED PHASE!)
- Capture and document failure messages

**IMPORTANT**: Always use the helper skills, never run tests manually via bash!

### Step 4: Analysis & Documentation
- Explain WHY tests failed (what's missing)
- List what needs to be implemented in GREEN PHASE
- Provide clear expectations for implementation

## Test Quality Guidelines

Good RED PHASE tests should be:

1. **Focused**: Test one thing at a time
2. **Clear**: Obvious what's being tested from the name
3. **Documented**: Comments explain the scenario and expectations
4. **Realistic**: Test real-world use cases, not artificial scenarios
5. **Independent**: Don't depend on other tests

### Example of Good RED PHASE Test

```php
/**
 * Test basic vacation carryover from previous year
 *
 * Scenario: Employee had 3 unused days from 2023
 * In 2024, they should have: 20 (new) + 3 (carryover) = 23 days
 *
 * EXPECTED TO FAIL: calculateAvailableDays() doesn't accept carryover parameter yet
 */
public function testBasicVacationCarryover(): void
{
    $hireDate = new DateTime('2023-01-01');
    $calculationDate = new DateTime('2024-01-01');
    $unusedDaysFromPreviousYear = 3;

    $result = $this->calculator->calculateAvailableDays(
        $hireDate,
        20,
        $calculationDate,
        $unusedDaysFromPreviousYear // This parameter doesn't exist yet!
    );

    $this->assertEquals(23, $result);
}
```

## Red Flags to Avoid

During RED PHASE, I should NOT:

- ❌ Implement the feature (that's GREEN PHASE)
- ❌ Try to make tests pass (they should FAIL)
- ❌ Change production code (only write tests)
- ❌ Modify test expectations to make them pass
- ❌ Skip running tests to verify they fail

## Test Types Supported

### Unit Tests (PHPUnit)
- **Execute via**: `/run_unit_tests` skill
- **Purpose**: Test individual functions/methods
- **Speed**: Fast, isolated tests
- **MCP**: Automatically uses MCP PHPUnit tools

### E2E Tests (Playwright)
- **Execute via**: `/run_e2e_tests` skill
- **Purpose**: Test complete user workflows
- **Speed**: Slower, full browser tests
- **Coverage**: Verify UI, controller, and service integration

### Integration Tests
- Test multiple components working together
- Database interactions, API calls, etc.
- Use appropriate skill based on test type

## Output Format

After completing RED PHASE, I should provide:

### 1. Summary
- Number of tests written
- Test types (unit/integration/e2e)
- Test execution results (X failed, Y passed)

### 2. Failure Analysis
- Why each test failed
- What's missing from implementation
- Code snippets showing gaps

### 3. Next Steps
- Clear instructions for GREEN PHASE
- List of components to implement
- Minimum changes needed to pass tests

## Example Usage

### Basic Feature Request
```
/run_red_phase Add email validation to user registration
```

### Detailed Requirements
```
/run_red_phase Feature: Password strength validation

Requirements:
- Minimum 8 characters
- Must contain uppercase, lowercase, number, special char
- Reject common passwords
- Provide helpful error messages

Edge cases to consider:
- Empty password
- Exactly 8 characters
- Unicode characters
- Very long passwords (>100 chars)
```

## Integration with Project

### Reading Configuration
Before writing tests, I should:
1. Read `CLAUDE.md` for project structure
2. Identify existing test patterns
3. Use same test framework and conventions
4. Follow project's naming conventions

### Using Test Execution Skills
- **ALWAYS use `/run_unit_tests`** instead of calling MCP tools directly
- **ALWAYS use `/run_e2e_tests`** instead of running playwright via bash
- **NEVER run tests manually** via bash or direct MCP calls
- These skills handle proper test execution and result analysis

**Why use skills?**
- ✅ Consistent test execution pattern
- ✅ Automatic MCP tool selection
- ✅ Proper result analysis
- ✅ Clear output formatting
- ✅ No path issues or mistakes

### Project Context
This skill is aware of:
- CakePHP 5 project structure
- Docker-based development environment
- PHPUnit and Playwright test frameworks
- TDD best practices from workshop materials

## Success Criteria

RED PHASE is successful when:
- ✅ Tests are written and well-documented
- ✅ Tests FAIL with clear error messages
- ✅ Failure shows what's missing
- ✅ Path to GREEN PHASE is clear
- ✅ No production code was changed

## Related Skills

### Test Execution (Used by this skill)
- `run_unit_tests`: Execute PHPUnit tests via MCP (used internally)
- `run_e2e_tests`: Execute Playwright E2E tests (used internally)

### TDD Workflow
- `run_green_phase`: Implement code to make RED tests pass (next step)
- `run_refactor_phase`: Improve code quality while keeping tests green
- `run_tdd_cycle`: Execute complete Red → Green → Refactor cycle

## Execution Workflow Example

Here's how I should execute RED PHASE:

### For Unit Tests:
```
1. Analyze requirements
2. Write PHPUnit tests in appropriate TestCase file
3. Use: /run_unit_tests path/to/TestFile.php
4. Verify tests FAIL
5. Document what needs to be implemented
```

### For E2E Tests:
```
1. Analyze requirements
2. Write Playwright tests in e2e/ directory
3. Use: /run_e2e_tests --grep "Feature Name"
4. Verify tests FAIL
5. Document what needs to be implemented
```

### Complete Example:
```
User: /run_red_phase Add vacation carryover feature

Me:
1. Read CLAUDE.md to understand project structure ✅
2. Analyze requirements:
   - Carryover = base_days - days_used_last_year
   - Should be optional parameter
   - Needs UI + backend
3. Write unit tests (VacationCalculatorTest.php)
4. /run_unit_tests tests/TestCase/Service/VacationCalculatorTest.php
   → Result: 2 tests FAIL ✅ (expected!)
5. Write E2E tests (e2e/vacation-calculator.spec.js)
6. /run_e2e_tests --grep "Carryover"
   → Result: 5 tests FAIL ✅ (expected!)
7. Document failures and next steps

RED PHASE COMPLETE! 🔴
Next: /run_green_phase to implement the feature
```

## Notes

- This skill embodies TDD philosophy: **test first, code later**
- Failing tests are SUCCESS in RED PHASE (they're a roadmap)
- The goal is NOT to pass tests, but to design through tests
- Clear, failing tests are more valuable than passing tests without thought
- **Always use helper skills** (`/run_unit_tests`, `/run_e2e_tests`) for test execution

---

**Remember**: In RED PHASE, red is green! 🔴 = ✅
