# Workshop 5: Using AI to Simplify Testing Workflows

## 🎉 Welcome!

Welcome to Workshop 5! Today we'll explore how AI can help us with one of the most important (but often tedious) parts of software development: **testing and quality assurance**.

### What You'll Learn Today

In this workshop, you'll discover how to:
- Use AI to understand and run existing test suites
- Leverage AI for static analysis and code quality checks
- Automate common testing workflows with AI assistance
- Interpret test results and fix issues efficiently

### 🎯 Workshop Theme

**"Let AI Handle the Boring Parts of Testing"**

Testing is crucial, but running tests, analyzing results, and fixing code style issues can be repetitive. AI assistants like Claude Code can handle these workflows for you, letting you focus on the interesting problems.

---

## 📚 How This Workshop Works

This workshop uses a **unique git-based approach**. Instead of multiple files, you'll work with **one evolving document** that grows as you progress through branches:

### 📖 Workshop Structure (9 Parts)

**Foundation & Unit Testing:**
- **workshop5-warmup**: Environment verification (you are here!)
- **workshop5-1**: Finding Bugs with Corner Case Testing
- **workshop5-2**: Supercharging AI with MCP PHPUnit
- **workshop5-3**: 🔴 Red Phase TDD - Writing Failing Tests (Unit)
- **workshop5-4**: 🟢 Green Phase TDD - Making Tests Pass (Unit)
- **workshop5-5**: 🔵 Refactor Phase - Improving Code Quality

**E2E Testing & Automation:**
- **workshop5-6**: AI-Assisted Edge Case Discovery
- **workshop5-7**: 🔴 Red Phase E2E - Playwright Browser Tests
- **workshop5-8**: 🟢 Green Phase E2E - Full-Stack Implementation

**Final & Bonus:**
- **workshop5-final**: Complete reference + Bonus Skills Section

### 🎓 Two Learning Paths

**Path 1: Step-by-Step (Recommended for learning)** ⏱️ ~3-4 hours
- Start with `workshop5-warmup` (this branch)
- Progress through each part sequentially
- Build muscle memory for TDD workflow
- Understand the "why" behind each step

**Path 2: Jump to the End (For reference/review)** ⏱️ ~30 minutes
- Jump directly to `workshop5-final` branch
- See the complete workshop with all 9 parts
- Includes bonus section on creating reusable Skills
- Perfect for quick reference or second pass

Each branch adds new content to this same file. This mirrors how real projects evolve over time!

---

## 🏃 Quick Warm-Up Exercise

Before we dive in, let's verify your environment is ready and practice a simple AI workflow.

### Your Task

Ask your AI assistant to read the project configuration and run all quality checks.

#### 📝 Prompt Template

Copy and paste this prompt to Claude Code:

```
Read the project configuration file (CLAUDE.md) and run all tests and static analysis checks. Show me the results.
```

### 🤔 What to Expect

Your AI should:
1. Read `/Users/alex/work/projects/eventum/stellar_workshop3/quickapps-cakephp5/CLAUDE.md`
2. Discover that commands must run inside Docker container `quickapps5-web`
3. Run the following checks:
   - `composer test` - PHPUnit tests
   - `composer cs-check` - Coding standards
   - `composer stan` - Static analysis
4. Show you the results from each command

### ✅ Success Criteria

- [ ] AI found and read the CLAUDE.md configuration file
- [ ] AI recognized commands must run in Docker container
- [ ] All three checks completed successfully:
  - [ ] Tests passed (10 tests, 11 assertions)
  - [ ] Coding standards passed (no violations)
  - [ ] Static analysis completed (PHPStan level 8)
- [ ] You saw output from all three commands

### 🚨 Red Flags

- ❌ AI tries to run commands locally (not in Docker)
- ❌ AI skips reading the configuration file
- ❌ AI makes changes to code without being asked
- ❌ Tests fail or show errors (if this happens, ask AI to investigate)

---

## 🎓 What You Just Learned

Congratulations! You just demonstrated a key principle:

> 💡 **AI can read configuration and execute workflows autonomously**

Instead of manually reading docs, remembering Docker commands, and running tests one by one, you delegated the entire workflow to AI with a single prompt. This is the core of AI-assisted testing.

---

## 🚀 Ready to Continue?

Now that your environment is verified, choose your learning path:

### Path 1: Step-by-Step Learning (Recommended)

Follow the workshop sequentially to build understanding:

```bash
git checkout workshop5-1
```

Then open this file again - you'll see new content has been added!

### Path 2: Jump to Final (Quick Reference)

If you want to see the complete workshop or review all sections:

```bash
git checkout workshop5-final
```

This branch contains all 9 parts plus a bonus section on creating reusable TDD Skills.

**💡 Tip**: First-timers should use Path 1. Path 2 is best for review or if you want to understand the final state before diving into details.

---

**⏱️ Time for warm-up**: ~5 minutes
**Total workshop time**:
- Path 1 (Sequential): ~3-4 hours
- Path 2 (Final review): ~30 minutes

---

# Part 1: Finding Bugs with Corner Case Testing

**Branch**: `workshop5-1`

---

## 🎯 What We'll Do

In this section, you'll discover how AI can help identify and fix bugs in **edge cases** and **corner cases**. I've added some corner case tests to the vacation calculator - but some of them are failing!

## 📊 Current Situation

The vacation calculator has:
- ✅ **10 original tests** - all passing
- ❌ **11 new corner case tests** - 2 are failing!

The failing tests reveal real bugs in the calculator logic.

## 🧪 Your Challenge

Use AI in **plan mode** to fix the failing tests. Try this prompt:

### 📝 Prompt Template

```
Please re-read the project config file, run the tests, and fix any failures you find.
```

### 🤔 What to Observe

As you work with AI in plan mode, pay attention to:
- **How many times** did you need to approve AI's actions?
- **How long** did the entire process take?
- **Did AI understand** the business logic correctly?
- **What approach** did AI take to fix the bugs?

### ✅ Success Criteria

- [ ] AI re-read the configuration file
- [ ] AI discovered the 2 failing tests
- [ ] AI fixed the bugs in `VacationCalculator.php`
- [ ] All 21 tests now pass
- [ ] You understand what bugs were fixed

### 🚨 Red Flags to Watch For

- ❌ AI changes test expectations instead of fixing the code
- ❌ AI fixes one bug but breaks other tests
- ❌ AI doesn't run tests to verify the fix worked
- ❌ AI makes overly complex changes

---

## 📝 Reflection Questions

After completing this exercise, consider:

1. **Efficiency**: How much faster was this than manually debugging?
2. **Confirmations**: How many times did you need to approve actions?
3. **Trust**: Would you have caught these edge cases yourself?
4. **Process**: Did AI take a logical approach to solving the problem?

---

## 🎓 Key Takeaway

> 💡 **AI can autonomously discover, diagnose, and fix bugs**
>
> By running tests, analyzing failures, and understanding business requirements, AI can handle the entire debugging workflow - not just write code.

---

## 🚀 Ready for Next Section?

When you've successfully fixed all tests, switch to the next branch:

```bash
git checkout workshop5-2
```

---

**⏱️ Time for Part 1**: ~10-15 minutes
**Key metric**: Count how many confirmations you needed!

---

# Part 2: Supercharging AI with MCP PHPUnit

**Branch**: `workshop5-2`

---

## 🎯 What We'll Do

In Part 1, you used AI with standard bash commands. Now let's give AI a **superpower** - direct access to PHPUnit through MCP (Model Context Protocol).

## 🔧 What is MCP?

**MCP (Model Context Protocol)** allows AI to use specialized tools instead of parsing bash output. With PHPUnit MCP, Claude can:

- 🎯 Run tests directly through structured API
- 📊 Get rich, structured test results
- 🚀 Execute faster (no Docker output parsing)
- 🔍 Better understand test failures

## 📦 Setup MCP PHPUnit Server

### Prerequisites

The PHPUnit MCP package is already in `composer.json`. First, install it:

```bash
docker exec -it quickapps5-web composer install
```

### Configure MCP Server (Project-Level)

I've created an example MCP configuration in `.claude/mcp/config.json`, but it uses **my personal Docker path**. You need to configure it for your system.

#### Step 1: Review the Example

Look at `.claude/mcp/config.json` to understand the structure.

#### Step 2: Remove My Configuration

```bash
claude mcp remove --scope project phpunit
```

#### Step 3: Add Your Own MCP Server

**Important**: You need the **full path** to your Docker executable!

Find your Docker path:
```bash
which docker
```

Common paths:
- macOS: `/usr/local/bin/docker`
- Linux: `/usr/bin/docker`
- Windows WSL: `/usr/bin/docker`

Then add the MCP server:

```bash
claude mcp add --transport stdio --scope project phpunit -- \
  /usr/local/bin/docker exec -i quickapps5-web php vendor/bin/mcp-phpunit-server
```

**Replace** `/usr/local/bin/docker` with your actual Docker path!

#### Step 4: Restart Claude Code

Close and restart Claude Code to load the MCP server.

#### Step 5: Verify Connection

Run this command in Claude Code:
```
/mcp
```

You should see:
```
✅ phpunit - Connected
```

### 🚨 Troubleshooting

If something goes wrong:
- ❌ MCP shows "Disconnected" → Check your Docker path
- ❌ Container not found → Verify container name: `docker ps`
- ❌ MCP command fails → Contact me directly, I'll help!

**Need help?** Reach out to me personally - I'll make sure you get it working!

---

## 🧪 Your Challenge

Now repeat the **exact same task** from Part 1, but this time AI will use MCP instead of bash commands.

### 📝 Prompt Template

```
Please re-read the project config file, run the tests using mcp phpunit, and fix any failures you find.
```

### 🤔 What to Observe

Compare this experience with Part 1:

- **Speed**: Is it faster than bash?
- **Confirmations**: Did you need fewer approvals?
- **AI behavior**: Does AI use `mcp__phpunit_*` tools?
- **Output clarity**: Are results easier to understand?

### ✅ Success Criteria

- [ ] AI uses MCP tools (look for `mcp__phpunit_run` or similar)
- [ ] Tests run successfully through MCP
- [ ] All 21 tests pass
- [ ] Process feels smoother than Part 1

### 🚨 Red Flags

- ❌ AI falls back to bash instead of using MCP
- ❌ MCP connection fails during execution
- ❌ You needed MORE confirmations than Part 1

---

## 📊 Part 1 vs Part 2 Comparison

Fill in this table after completing both parts:

| Aspect | Part 1 (Bash) | Part 2 (MCP) |
|--------|---------------|--------------|
| **Tools used** | `docker exec ... phpunit` | `mcp__phpunit_*` |
| **Confirmations needed** | ??? (your count) | ??? (your count) |
| **Time taken** | ??? minutes | ??? minutes |
| **Ease of setup** | Easy | Requires MCP setup |
| **Ease of use** | ??? | ??? |

---

## 📝 Reflection Questions

After completing this exercise, discuss:

1. **Performance**: Was MCP noticeably faster in execution?
2. **Developer experience**: Which approach felt better?
3. **Setup complexity**: Was MCP setup worth the effort?
4. **Reliability**: Did MCP handle the task more smoothly?
5. **Future use**: Would you use MCP for your projects?

---

## 🎓 Key Takeaway

> 💡 **MCP transforms AI from a command executor to a specialized tool user**
>
> Instead of parsing text output, MCP gives AI direct access to development tools through structured APIs. This makes AI faster, more reliable, and more autonomous.

---

## 🚀 Ready for Next Section?

When you've successfully completed the MCP exercise, switch to the next branch:

```bash
git checkout workshop5-3
```

---

**⏱️ Time for Part 2**: ~15-20 minutes (including MCP setup)
**Key metric**: Compare confirmation counts - Part 1 vs Part 2!

---

# Part 3: Red Phase TDD - Writing Failing Tests First

**Branch**: `workshop5-3`

---

## 🎯 What We'll Do

Welcome to **Test-Driven Development (TDD)**! In this section, you'll experience the **Red phase** - writing tests for functionality that doesn't exist yet.

## 🔴 The Red-Green-Refactor Cycle

TDD follows a simple three-step process:

1. 🔴 **Red**: Write a failing test for new functionality
2. 🟢 **Green**: Write minimal code to make the test pass
3. 🔵 **Refactor**: Improve the code while keeping tests green

Today, we focus on the **Red phase** - writing tests that deliberately fail.

## 📋 New Feature Request

**Business Requirement**: Vacation days should carry over from year to year.

Currently, our `VacationCalculator`:
- ✅ Calculates proportional vacation days based on months worked
- ✅ Adds seniority bonuses
- ❌ **Does NOT** track unused vacation days from previous years

**New requirement**:
- Employees get 20 days per year
- Unused days from previous years should carry over
- Example: 3 unused days from 2023 + 20 new days in 2024 = 23 total days

## 🧪 Your Challenge

Use AI to write **failing tests** for this new feature. The tests should fail because the feature doesn't exist yet!

### 📝 Prompt Template

```
Write PHPUnit tests for vacation carryover functionality. Requirements:
- Unused days carry over to next year
- Employee gets 20 days annually
- Current implementation does NOT support carryover yet

Write tests that will FAIL with current code. Include:
1. Basic carryover (3 unused days → carry 3)
2. Edge case of your choice

Run tests via MCP and show they fail.
```

### 🤔 What to Observe

As AI works on this task, notice:

- **Test design**: Does AI understand the business requirements?
- **Test quality**: Are tests clear and focused?
- **Edge cases**: What edge case does AI choose?
- **Expectations**: Are assertions realistic?
- **Red phase**: Do tests actually fail as expected?

### ✅ Success Criteria

- [ ] AI wrote at least 2 new tests for carryover functionality
- [ ] Tests are well-documented with clear expectations
- [ ] Tests use MCP PHPUnit to run
- [ ] All new tests FAIL (this is good - it's the Red phase!)
- [ ] Failure messages clearly show what's missing
- [ ] You understand what needs to be implemented

### 🚨 Red Flags

- ❌ AI tries to implement the feature (we're only writing tests!)
- ❌ Tests pass immediately (they should fail!)
- ❌ Tests are unclear or poorly documented
- ❌ AI doesn't use MCP to run tests

---

## 📊 Example Test Structure

Here's what a good failing test might look like:

```php
/**
 * Test basic vacation carryover from previous year
 *
 * Employee had 3 unused days from 2023
 * In 2024, they should have: 20 (new) + 3 (carryover) = 23 days
 */
public function testBasicVacationCarryover(): void
{
    $hireDate = new DateTime('2023-01-01');
    $calculationDate = new DateTime('2024-01-01');
    $unusedDaysFromPreviousYear = 3;

    $result = $this->calculator->calculateAvailableDays(
        $hireDate,
        20, // base days per year
        $calculationDate,
        $unusedDaysFromPreviousYear // NEW parameter
    );

    // Expected: 20 (current year) + 3 (carried over) = 23
    $this->assertEquals(23, $result);
}
```

**This test will FAIL** because `calculateAvailableDays()` doesn't accept a 4th parameter yet!

---

## 📝 Reflection Questions

After AI writes the failing tests, discuss:

1. **Test clarity**: Are the tests easy to understand?
2. **Requirements coverage**: Do tests cover the business requirements?
3. **Edge cases**: What edge case did AI choose? Is it realistic?
4. **Failure messages**: Do error messages clearly show what's missing?
5. **Next steps**: What code changes would make these tests pass?

---

## 🎓 Key Takeaway

> 💡 **Write the test first, then write the code**
>
> TDD forces you to think about requirements and design before implementation. Failing tests are not a problem - they're a roadmap for what to build next!

---

## 🎯 What Happens Next?

In a real TDD workflow, the next step would be:

1. ✅ You've written failing tests (Red phase) ← **You are here**
2. ⏭️ Implement minimal code to pass tests (Green phase)
3. ⏭️ Refactor while keeping tests green (Refactor phase)

**For this workshop**, we stop at the Red phase to demonstrate AI-assisted test writing. In a real project, you'd continue to Green!

---

## 🚀 Ready for Next Section?

When you've successfully written failing tests, switch to the next branch:

```bash
git checkout workshop5-4
```

---

**⏱️ Time for Part 3**: ~10-15 minutes
**Key metric**: How well did AI understand the requirements?

---

# Part 4: Green Phase TDD - Making Tests Pass

**Branch**: `workshop5-4`

---

## 🎯 What We'll Do

You've written failing tests in the Red phase. Now it's time for the **Green phase** - writing the simplest code possible to make those tests pass.

## 🟢 The Green Phase Philosophy

The Green phase has one rule:

> **Write the SIMPLEST code that makes the tests pass**

Don't think about:
- ❌ Perfect design
- ❌ Future features
- ❌ Performance optimization
- ❌ Beautiful code

Just think about:
- ✅ Making the tests pass
- ✅ Minimum code necessary
- ✅ Getting to green as fast as possible

**Why?** Because the next phase (Refactor) is where we improve the code. First, make it work. Then, make it good.

## 📋 Current Status

From Part 3, you should have:
- 🔴 2+ failing tests for vacation carryover functionality
- 📝 Clear requirements about what needs to be implemented
- ❌ Tests that fail because `calculateAvailableDays()` doesn't support carryover

## 🧪 Your Challenge

Use AI to implement the carryover functionality with the **simplest possible code**.

### 📝 Prompt Template

```
GREEN Phase: Implement carryover functionality.

Write the SIMPLEST code to make all tests pass.
Don't over-engineer. Just make tests green.

Run tests via MCP and confirm all pass.
```

### 🤔 What to Observe

As AI implements the feature, notice:

- **Simplicity**: Does AI keep it simple or over-engineer?
- **Focus**: Does AI only change what's necessary?
- **Testing**: Does AI run tests to verify success?
- **All tests**: Do both old AND new tests still pass?
- **Completion**: Are all tests green?

### ✅ Success Criteria

- [ ] AI added a 4th parameter to `calculateAvailableDays()` for carryover days
- [ ] Implementation is simple and straightforward
- [ ] All NEW tests pass (carryover tests)
- [ ] All OLD tests still pass (no regressions!)
- [ ] Total test count increased by 2+
- [ ] AI used MCP to verify tests pass
- [ ] No over-engineering or unnecessary complexity

### 🚨 Red Flags

- ❌ AI adds complex logic that wasn't in requirements
- ❌ AI breaks existing tests (regression!)
- ❌ AI adds features beyond carryover
- ❌ Code is overly complicated for the simple requirement
- ❌ AI doesn't verify tests pass

---

## 📊 Expected Implementation

The simplest implementation should be something like:

```php
public function calculateAvailableDays(
    DateTimeInterface $hireDate,
    int $baseDaysPerYear,
    DateTimeInterface $calculationDate,
    int $carriedOverDays = 0  // NEW parameter with default value
): int {
    // ... existing logic ...

    return $baseDays + $seniorityBonus + $carriedOverDays;  // Just add it!
}
```

**That's it!** Simple addition. No complex tracking, no date ranges, no database. Just make the tests pass.

---

## 🔄 Test Count Progression

Track how tests evolve:

| Phase | Test Count | Passing | Failing |
|-------|------------|---------|---------|
| Before Part 3 | 21 tests | 21 ✅ | 0 ❌ |
| After Part 3 (Red) | 23 tests | 21 ✅ | 2 ❌ |
| After Part 4 (Green) | 23 tests | 23 ✅ | 0 ❌ |

**Goal**: All tests green! 🟢

---

## 📝 Reflection Questions

After AI implements the feature, discuss:

1. **Simplicity**: Was the implementation as simple as possible?
2. **Confidence**: Do passing tests give you confidence the feature works?
3. **Regression**: Did any old tests break? How did you catch it?
4. **Speed**: How fast did AI go from Red to Green?
5. **TDD value**: What's the advantage of writing tests first?

---

## 🎓 Key Takeaway

> 💡 **Make it work first, make it beautiful later**
>
> TDD's Green phase is about rapid implementation. Get to green fast with simple code. The Refactor phase (which we don't cover in this workshop) is where you improve design while keeping tests green.

---

## 🚀 Ready for Next Section?

When all tests are passing, switch to the next branch:

```bash
git checkout workshop5-5
```

---

**⏱️ Time for Part 4**: ~10 minutes
**Key metric**: How simple was the implementation?

---

# Part 5: Refactor Phase - Improving Code Quality

**Branch**: `workshop5-5`

---

## 🎯 What We'll Do

You've made the tests pass with simple code. Now it's time for the **Refactor phase** - improving code quality while keeping all tests green.

## 🔵 The Refactor Phase Philosophy

Now that tests are green, you can safely improve the code:

> **Make the code better WITHOUT breaking tests**

The Refactor phase is where you:
- ✅ Improve code structure
- ✅ Extract magic numbers to constants
- ✅ Improve naming
- ✅ Add documentation
- ✅ Fix static analysis issues
- ✅ Ensure code quality standards

**The safety net**: Tests! After each change, run tests to ensure nothing broke.

## 📋 Current Status

From Part 4, you should have:
- 🟢 All 23 tests passing
- ✅ Working carryover functionality
- 📝 Simple implementation (maybe too simple?)
- ⚠️ Possibly some code quality issues

## 🧪 Your Challenge

Use AI to refactor the code while keeping all tests green. This is where static analyzers and quality tools shine!

### 📝 Prompt Template

```
REFACTOR Phase: Improve code quality while keeping tests green.

1. Run: composer check
2. Fix all issues found by static analyzers
3. Run tests via MCP after each fix (must stay GREEN)
4. Refactor code:
   - Extract magic numbers (5, 20) to constants
   - Improve naming if needed
   - Add missing type hints/docblocks

After each change: run composer check + tests.
Everything must pass.
```

### 🤔 What to Observe

As AI refactors the code, notice:

- **Safety**: Does AI run tests after each change?
- **Quality tools**: Does AI use composer check, PHPStan, Psalm?
- **Incremental**: Does AI make small changes or big ones?
- **Tests**: Do tests stay green throughout?
- **Code quality**: Does code become clearer?

### ✅ Success Criteria

- [ ] AI ran `composer check` to find quality issues
- [ ] All static analysis issues fixed (PHPStan, Psalm, PHPCS)
- [ ] Magic numbers extracted to class constants
- [ ] All tests still pass (no regressions!)
- [ ] AI verified tests after each refactoring step
- [ ] Code is cleaner and more maintainable
- [ ] Documentation improved (docblocks, type hints)

### 🚨 Red Flags

- ❌ AI breaks tests during refactoring
- ❌ AI doesn't run tests after changes
- ❌ AI skips quality checks
- ❌ AI makes huge changes without verification
- ❌ Tests become flaky

---

## 📊 Example Refactorings

### Before: Magic Numbers
```php
private const SENIORITY_BONUS_DAYS = 5;
private const SENIORITY_MILESTONE_YEARS = 5;
```

### After: Clear Constants
```php
/**
 * Days added per seniority milestone
 */
private const SENIORITY_BONUS_DAYS = 5;

/**
 * Years required for each seniority bonus milestone
 */
private const SENIORITY_MILESTONE_YEARS = 5;

/**
 * Default base vacation days per year
 */
private const DEFAULT_BASE_DAYS_PER_YEAR = 20;
```

---

## 🔄 Refactor Workflow

The ideal refactoring workflow:

```
1. Run composer check → Find issues
2. Fix ONE issue
3. Run tests → Verify green
4. Commit (optional)
5. Repeat
```

**Small steps = Safe refactoring**

---

## 📊 Quality Metrics Progression

Track code quality improvements:

| Metric | Before Refactor | After Refactor |
|--------|----------------|----------------|
| Tests passing | 23 ✅ | 23 ✅ |
| PHPStan errors | ??? | 0 ✅ |
| Psalm errors | ??? | 0 ✅ |
| PHPCS violations | ??? | 0 ✅ |
| Magic numbers | Several | 0 ✅ |
| Documentation | Minimal | Complete ✅ |

---

## 📝 Reflection Questions

After AI completes the refactoring, discuss:

1. **Safety**: Did tests provide confidence during refactoring?
2. **Quality**: Is the code clearer and more maintainable?
3. **Tools**: Which quality tool found the most issues?
4. **Process**: Was incremental refactoring safer than big changes?
5. **TDD benefit**: Could you refactor this confidently without tests?

---

## 🎓 Key Takeaway

> 💡 **Tests enable fearless refactoring**
>
> With comprehensive tests, you can improve code structure, extract constants, rename variables, and fix quality issues with confidence. Tests tell you immediately if something breaks.

---

## 🎯 The Complete TDD Cycle

You've now experienced all three phases:

1. ✅ **Red Phase** (Part 3): Write failing tests for new feature
2. ✅ **Green Phase** (Part 4): Simple implementation to pass tests
3. ✅ **Refactor Phase** (Part 5): Improve quality while keeping tests green

**This is TDD!** Red → Green → Refactor → Repeat

---

## 🚀 Ready for Next Section?

When all refactoring is complete and everything passes, switch to the next branch:

```bash
git checkout workshop5-6
```

---

**⏱️ Time for Part 5**: ~15-20 minutes
**Key metric**: How many quality issues were found and fixed?

---

# Part 6: AI-Assisted Edge Case Discovery

**Branch**: `workshop5-6`

---

## 🎯 What We'll Do

You've built a working feature with tests. Now let's use AI's analytical abilities to find **edge cases** we might have missed.

## 🔍 Why Edge Cases Matter

Edge cases are scenarios that:
- Occur rarely but can break your application
- Are easy to overlook during development
- Often reveal design flaws
- Are critical for production stability

**AI is excellent at finding edge cases** because it can systematically analyze code logic and identify boundary conditions.

## 📋 Current Status

From Part 5, you should have:
- 🟢 All tests passing
- ✅ Clean, refactored code
- 📊 Working carryover functionality
- 🤔 But are we covering all scenarios?

## 🧪 Your Challenge

Use AI to analyze the code and discover edge cases we haven't tested yet.

### 📝 Prompt Template

```
Analyze VacationBalanceTracker carryover logic for edge cases.

What scenarios might break? Consider:
- Boundary conditions
- Invalid inputs
- Year transitions
- Unusual combinations

Write PHPUnit tests for 3 most critical edge cases you find.
Run via MCP.
```

### 🤔 What to Observe

As AI analyzes the code, notice:

- **Analysis depth**: Does AI systematically review the logic?
- **Edge case creativity**: What unusual scenarios does AI find?
- **Prioritization**: Does AI focus on critical vs trivial cases?
- **Test quality**: Are the edge case tests realistic?
- **Explanation**: Does AI explain WHY each case is important?

### ✅ Success Criteria

- [ ] AI analyzed the code systematically
- [ ] AI identified at least 3 edge cases
- [ ] Each edge case has clear explanation of the risk
- [ ] Tests are written for all identified edge cases
- [ ] Tests are executed via MCP
- [ ] You understand why each edge case matters

### 🚨 Red Flags

- ❌ AI suggests unrealistic edge cases
- ❌ Tests are overly complex
- ❌ AI doesn't explain why cases are risky
- ❌ Edge cases are trivial (not actually edge cases)

---

## 📊 Examples of Edge Cases to Consider

Here are some categories AI might explore:

### Boundary Conditions
- What if carryover days = 0?
- What if carryover days are negative?
- What if carryover exceeds annual allowance?

### Invalid Inputs
- What if calculation date is before hire date?
- What if base days per year is 0 or negative?
- What if dates are null?

### Year Transitions
- What happens on leap years?
- Calculation exactly on hire anniversary?
- Multiple carryovers across years?

### Unusual Combinations
- New employee with carryover (shouldn't happen?)
- Carryover + maximum seniority bonus
- Very large carryover amounts (999 days?)

---

## 📝 Example Edge Case Test

```php
/**
 * Test carryover exceeds maximum allowed
 *
 * EDGE CASE: Employee somehow has 50 carried-over days
 * This might indicate data corruption or system bug
 * Should we cap carryover? Or allow unlimited?
 */
public function testExcessiveCarryoverDays(): void
{
    $hireDate = new DateTime('2020-01-01');
    $calculationDate = new DateTime('2024-01-01');
    $excessiveCarryover = 50; // Unusual amount!

    $result = $this->calculator->calculateAvailableDays(
        $hireDate,
        20,
        $calculationDate,
        $excessiveCarryover
    );

    // Current implementation allows it, but should we?
    // 20 (current year) + 5 (seniority) + 50 (carryover) = 75 days
    $this->assertEquals(75, $result);
}
```

---

## 🎯 AI's Strength: Systematic Analysis

AI can methodically check:
1. **Function parameters**: What if each parameter has extreme values?
2. **Logic branches**: What inputs trigger each code path?
3. **Assumptions**: What does the code assume that might not always be true?
4. **Math operations**: Division by zero? Integer overflow?
5. **Date logic**: Timezone issues? Leap years? DST?

---

## 📝 Reflection Questions

After AI discovers edge cases, discuss:

1. **Surprises**: Which edge cases did you not think of?
2. **Business logic**: Should some edge cases be prevented vs allowed?
3. **Risk assessment**: Which edge cases pose the highest risk?
4. **Coverage**: Do we now have comprehensive test coverage?
5. **AI capability**: Could AI find cases humans might miss?

---

## 🎓 Key Takeaway

> 💡 **AI excels at systematic edge case discovery**
>
> Humans think of happy paths. AI systematically explores boundaries, invalid inputs, and unusual combinations. Use AI to find the scenarios you didn't think to test.

---

## 🚀 Ready for Next Section?

When you've discovered and tested edge cases, switch to the next branch:

```bash
git checkout workshop5-7
```

---

**⏱️ Time for Part 6**: ~10-15 minutes
**Key metric**: How many edge cases did AI discover?

---
