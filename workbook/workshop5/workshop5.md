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

<<<<<<< HEAD
# Part 1: Finding Bugs with Corner Case Testing

**Branch**: `workshop5-1`
=======
**⏱️ Time for warm-up**: ~5 minutes
**Total workshop time**:
- Path 1 (Sequential): ~3-4 hours
- Path 2 (Final review): ~30 minutes
>>>>>>> 3549c40 (Workshop5 Update workshop structure: Add complete 9-part outline and two learning paths)

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

After completing the MCP setup and testing, discuss:

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

## 🚀 Ready to Continue?

Next: `git checkout workshop5-3`

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

> 🔑 **Write the test first, then write the code**
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

> 🔑 **Make it work first, make it beautiful later**
>
> TDD's Green phase is about rapid implementation. Get to green fast with simple code. The Refactor phase (which we don't cover in this workshop) is where you improve design while keeping tests green.

---

## 🚀 Ready to Continue?

Next: `git checkout workshop5-5`

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

After refactoring is complete, discuss:

1. **Safety**: Did tests provide confidence during refactoring?
2. **Quality**: Is the code clearer and more maintainable?
3. **Tools**: Which quality tool found the most issues?
4. **Process**: Was incremental refactoring safer than big changes?
5. **TDD benefit**: Could you refactor this confidently without tests?

---

## 🎓 Key Takeaway

> ⚡ **Tests enable fearless refactoring**
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

Once edge cases are discovered and tested, consider:

1. **Surprises**: Which edge cases did you not think of?
2. **Business logic**: Should some edge cases be prevented vs allowed?
3. **Risk assessment**: Which edge cases pose the highest risk?
4. **Coverage**: Do we now have comprehensive test coverage?
5. **AI capability**: Could AI find cases humans might miss?

---

## 🎓 Key Takeaway

> ⚡ **AI excels at systematic edge case discovery**
>
> Humans think of happy paths. AI systematically explores boundaries, invalid inputs, and unusual combinations. Use AI to find the scenarios you didn't think to test.

---

## 🚀 Ready to Continue?

Next: `git checkout workshop5-7`

---

**⏱️ Time for Part 6**: ~10-15 minutes
**Key metric**: How many edge cases did AI discover?

---

# Part 7: E2E Testing with Playwright MCP

**Branch**: `workshop5-7`

---

## 🎯 What We'll Do

Unit tests verify individual functions work correctly. But does the **entire application** work? That's where **End-to-End (E2E) tests** come in. We'll use Playwright with MCP to test the complete user workflow.

## 🌐 What is E2E Testing?

**E2E tests** simulate real user interactions:
- Opening the browser
- Filling out forms
- Clicking buttons
- Verifying results on the page

**Why E2E matters:**
- ✅ Unit tests passed, but UI might be broken
- ✅ Integration issues between frontend and backend
- ✅ User experience validation
- ✅ Real browser environment

## 🎭 Playwright MCP Capabilities

The **Playwright MCP server** gives AI powerful browser automation:

### Navigation & Control
- `browser_navigate` - go to URLs
- `browser_click` - interact with elements
- `browser_type` - fill forms
- `browser_snapshot` - capture page state

### Testing & Verification
- `browser_take_screenshot` - visual snapshots
- `browser_console_messages` - check for JS errors
- `browser_network_requests` - monitor API calls
- `browser_wait_for` - wait for elements

### Smart Features
- Uses accessibility tree (better than DOM selectors)
- Handles async operations automatically
- Multi-browser support (Chromium, Firefox, WebKit)

## 📋 Current Status

From Part 6, you have:
- 🟢 Unit tests with edge cases
- ✅ Backend carryover logic implemented
- ❌ But UI doesn't have carryover field yet!

**Perfect scenario for E2E testing!** Unit tests pass, but the feature isn't usable.

## 🧪 Your Challenge

Write E2E tests that verify the carryover functionality works in the browser. These tests will FAIL (RED phase) because the UI isn't implemented yet.

### 📝 Prompt Template

```
Write E2E tests for vacation carryover feature using Playwright.

Use Context7 to get latest Playwright documentation.

Tests should verify:
- "Days Used Last Year" field exists in form
- Carryover calculation works correctly
- Results display carryover breakdown
- Optional field behavior (can be left empty)
- Edge cases (negative values, excess usage)

Run tests via MCP Playwright. They should FAIL (RED phase).
```

### 🤔 What to Observe

As AI writes E2E tests, notice:

- **Context7 usage**: Does AI fetch latest Playwright docs?
- **Test structure**: Are tests clear and maintainable?
- **Selectors**: Does AI use semantic selectors (roles, labels)?
- **Assertions**: Are expectations realistic?
- **MCP integration**: Does AI run tests via MCP?
- **Failure analysis**: Does AI explain WHY tests fail?

### ✅ Success Criteria

- [ ] AI used Context7 to get Playwright documentation
- [ ] 5-6 E2E tests written for carryover feature
- [ ] Tests use semantic selectors (getByLabel, getByRole)
- [ ] Tests executed via MCP Playwright
- [ ] 5 tests FAIL as expected (RED phase)
- [ ] 1 test PASSES (optional field scenario)
- [ ] AI explains what's missing in UI

### 🚨 Red Flags

- ❌ AI uses fragile selectors (CSS classes, IDs)
- ❌ AI tries to implement UI (should only write tests!)
- ❌ Tests are too complex or coupled
- ❌ AI doesn't run tests to verify they fail

---

## 📊 Expected Test Results

### Tests That Should FAIL (5):

1. ❌ **Field existence**: "Days Used Last Year" field doesn't exist
2. ❌ **Basic carryover**: Can't test calculation without field
3. ❌ **Zero carryover**: Can't fill field that doesn't exist
4. ❌ **Edge cases**: No UI to test edge cases
5. ❌ **Results display**: No carryover section in results

### Test That Should PASS (1):

✅ **Optional field**: Form works without carryover field (current behavior)

## 🎓 E2E vs Unit Testing

| Aspect | Unit Tests | E2E Tests |
|--------|-----------|-----------|
| **Scope** | Single function | Entire workflow |
| **Speed** | Fast (milliseconds) | Slower (seconds) |
| **Isolation** | Isolated | Integrated |
| **Catches** | Logic bugs | Integration issues |
| **When to run** | Every save | Before deploy |
| **Example** | `calculateAvailableDays()` returns 23 | User sees "23 days" on page |

**Both are essential!** Unit tests for logic, E2E tests for user experience.

---

## 📝 Example E2E Test

```javascript
test('should calculate carryover when employee has unused vacation days', async ({ page }) => {
  // Navigate to calculator
  await page.goto('http://localhost:8090');

  // Fill form
  await page.getByLabel('Hire Date *').fill('2020-01-01');
  await page.getByLabel('Base Vacation Days Per Year *').fill('20');
  await page.getByLabel('Days Used Last Year').fill('17'); // WILL FAIL - field doesn't exist

  // Submit
  await page.getByRole('button', { name: 'Calculate' }).click();

  // Verify results
  await expect(page.locator('.result-item').filter({ hasText: 'Carryover Days' }))
    .toContainText('+3 days'); // WILL FAIL - no carryover in results
});
```

**This test documents what SHOULD work**, even though it doesn't yet.

---

## 🔧 Using Context7 for Documentation

AI can fetch the latest Playwright docs to ensure tests use current best practices:

```
Use Context7 to get latest Playwright documentation for:
- Form interactions
- Assertions
- Waiting for elements
- Semantic selectors
```

**Benefits:**
- Always up-to-date API usage
- Best practices from official docs
- No outdated examples

---

## 📝 Reflection Questions

After writing E2E tests, reflect on:

1. **Coverage**: What issues can E2E catch that unit tests can't?
2. **Maintenance**: Are E2E tests harder to maintain than unit tests?
3. **Failure clarity**: When E2E test fails, is it clear what's broken?
4. **MCP value**: Did MCP Playwright make testing easier?
5. **Documentation**: How helpful was Context7 for getting current docs?

---

## 🎓 Key Takeaway

> 🎯 **E2E tests verify the user experience, not just the code**
>
> Unit tests passing doesn't mean users can use the feature. E2E tests simulate real user interactions and catch integration issues. With MCP Playwright, AI can write and run browser tests automatically.

---

## 🎯 The Testing Pyramid

```
       /\
      /E2E\      ← Few, slow, broad coverage
     /------\
    /Integration\ ← Medium number, medium speed
   /------------\
  /  Unit Tests  \ ← Many, fast, focused
 /----------------\
```

**This workshop covered:**
- ✅ **Unit tests**: VacationCalculator logic
- ✅ **E2E tests**: Full user workflow
- ⏭️ **Integration tests**: Not covered, but sit in the middle

---

## 🚀 Moving Forward

When E2E tests are written and failing (RED phase confirmed), switch to the next branch:

```bash
git checkout workshop5-8
```

---

**⏱️ Time for Part 7**: ~15-20 minutes
**Key metric**: How many E2E tests failed vs passed?

---

# Part 8: GREEN Phase E2E - Making Tests Pass

**Branch**: `workshop5-8`

---

## 🎯 What We'll Do

E2E tests are failing (RED phase complete). Now implement the **minimal UI changes** to make all tests pass (GREEN phase).

## 🟢 GREEN Phase for E2E

The GREEN phase for E2E is different from unit tests:
- **Unit tests**: Just fix the function
- **E2E tests**: Fix form + controller + results display

But the principle is the same: **Make the simplest changes to pass tests**.

## 📋 Current Status

From Part 7, you have:
- ❌ 5 E2E tests failing (expected!)
- ✅ 1 E2E test passing (optional field scenario)
- ✅ All unit tests passing (backend works)

**What's missing:** UI implementation for carryover feature.

## 🧪 Your Challenge

Implement the UI for carryover functionality. Keep it simple - just make tests green.

### 📝 Prompt Template

```
GREEN Phase E2E: Implement carryover UI to make tests pass.

Requirements:
1. Add "Days Used Last Year" field to form (optional)
2. Update controller to pass parameter to service
3. Display carryover in results (only when provided)

Keep it SIMPLE. Just make tests green.
Run E2E tests via Playwright after changes.
Run unit tests via MCP PHPUnit to verify no regressions.
```

### 🤔 What to Observe

As AI implements the UI, notice:

- **Simplicity**: Does AI keep changes minimal?
- **Testing**: Does AI run tests after each change?
- **Completeness**: Are all three parts implemented?
- **Backward compatibility**: Do old tests still pass?
- **MCP usage**: Does AI use both Playwright and PHPUnit MCP?

### ✅ Success Criteria

- [ ] Form field added (optional number input, 0-365 range)
- [ ] Controller updated (parse parameter, pass to service)
- [ ] Results display updated (show carryover section)
- [ ] All 11 E2E tests pass (6 new + 5 old)
- [ ] All 25 unit tests pass (no regressions)
- [ ] AI verified with both Playwright and MCP PHPUnit

### 🚨 Red Flags

- ❌ AI over-engineers the solution
- ❌ AI breaks existing functionality
- ❌ AI doesn't test after implementation
- ❌ Implementation is incomplete (missing one of three parts)

---

## 📊 What Gets Implemented

### 1. Form Field (templates/Pages/home.php)

```php
<div class="form-group">
    <label for="days_used_last_year">Days Used Last Year</label>
    <input
        type="number"
        id="days_used_last_year"
        name="days_used_last_year"
        min="0"
        max="365"
        value="<?= $this->request->getData('days_used_last_year') ?? '' ?>"
    >
    <p class="help-text">Days you used in the previous year (leave empty to skip carryover calculation)</p>
</div>
```

**Key features:**
- Optional (no `required` attribute)
- Number input with validation (0-365)
- Help text explains usage
- Preserves value on form resubmit

### 2. Controller Logic (Controller/PagesController.php)

```php
// Parse carryover parameter (optional)
$daysUsedLastYear = null;
if (isset($data['days_used_last_year']) && $data['days_used_last_year'] !== '') {
    $daysUsedLastYear = (int)$data['days_used_last_year'];
}

// Pass to service
$availableDays = $calculator->calculateAvailableDays(
    $hireDate,
    $baseDaysPerYear,
    $calculationDate,
    $daysUsedLastYear,  // NEW parameter
);

// Calculate carryover for display
$carryoverDays = null;
if ($daysUsedLastYear !== null) {
    $carryoverDays = max(0, $baseDaysPerYear - $daysUsedLastYear);
}

$result = [
    // ... existing fields ...
    'carryover_days' => $carryoverDays,
    'days_used_last_year' => $daysUsedLastYear,
];
```

**Key logic:**
- Check if parameter exists and not empty
- Convert to int or leave as null
- Calculate carryover for display (same logic as service)

### 3. Results Display (templates/Pages/home.php)

```php
<?php if ($result['carryover_days'] !== null): ?>
    <div class="result-item">
        <div class="result-label">Carryover Days</div>
        <div class="result-value">
            +<?= h($result['carryover_days']) ?> days
            <small style="color: #7f8c8d;">(unused from previous year)</small>
        </div>
    </div>
<?php endif; ?>
```

**Key features:**
- Only shows when carryover provided
- Matches existing result-item styling
- Clear labeling with explanation

---

## 📊 Expected Test Results

### After Implementation:

| Test Suite | Before | After | Status |
|------------|--------|-------|--------|
| E2E Tests (Playwright) | 1/6 pass | 11/11 pass | ✅ All green |
| Unit Tests (PHPUnit) | 25/25 pass | 25/25 pass | ✅ No regressions |
| **Total** | **26/31** | **36/36** | **🎉 100%** |

---

## 🔄 Testing Workflow

AI should follow this workflow:

1. **Implement form field** → Run Playwright tests
2. **Update controller** → Run Playwright tests
3. **Add results display** → Run Playwright tests
4. **Final verification** → Run both Playwright AND PHPUnit tests

**Why test after each step?** Catch issues early, not at the end.

---

## 📝 Reflection Questions

Now that the UI is implemented and working, discuss:

1. **Simplicity**: Was the implementation as simple as possible?
2. **Testing confidence**: Did passing tests give you confidence?
3. **E2E value**: Did E2E tests catch issues unit tests missed?
4. **Development speed**: How fast was the RED → GREEN cycle?
5. **Real-world**: Would you use this TDD approach in production?

---

## 🎓 Key Takeaway

> 🎯 **E2E tests drive full-stack implementation**
>
> Unlike unit tests that test one function, E2E tests require changes across multiple layers (UI, controller, service). This ensures the entire feature works together. TDD with E2E tests provides confidence that users can actually use the feature.

---

## 🎯 Complete TDD Cycle Achieved

You've now completed the full TDD cycle at **two levels**:

### Unit Test TDD (Parts 3-5):
1. 🔴 **RED**: Write failing unit tests
2. 🟢 **GREEN**: Implement service logic
3. 🔵 **REFACTOR**: Improve code quality

### E2E Test TDD (Parts 7-8):
1. 🔴 **RED**: Write failing E2E tests
2. 🟢 **GREEN**: Implement full UI
3. 🔵 **REFACTOR**: (Not needed, kept it simple!)

**This is comprehensive TDD!** Tests at multiple levels, all driving development.

---

## 🚀 Ready for the Finale?

When all tests pass and the feature works, switch to the final branch:

```bash
git checkout workshop5-final
```

---

**⏱️ Time for Part 8**: ~15-20 minutes
**Key metric**: Did all 36 tests pass?

---

# 🎉 Congratulations! Workshop Complete!

**Branch**: `workshop5-final`

---

## 🏆 What You've Accomplished

You've just completed a comprehensive journey through AI-assisted testing workflows! Let's recap what you've achieved:

### 📊 Skills Acquired

- ✅ **Testing Fundamentals**
  - Understand the testing pyramid (Unit → Integration → E2E)
  - Run tests with confidence using Docker and MCP tools
  - Interpret test results and identify regressions
  - Fix bugs discovered through automated testing

- ✅ **TDD Mastery**
  - 🔴 **RED Phase**: Write failing tests that document requirements
  - 🟢 **GREEN Phase**: Implement minimal code to pass tests
  - 🔵 **REFACTOR Phase**: Improve code quality with test safety net
  - Complete the full TDD cycle at multiple levels (Unit + E2E)

- ✅ **MCP Tools Integration**
  - Configure and use PHPUnit MCP for structured test execution
  - Leverage Playwright MCP for browser-based E2E testing
  - Use Context7 for up-to-date library documentation
  - Understand MCP advantages over bash commands

- ✅ **Quality Assurance**
  - Run static analysis (PHPStan level 8)
  - Enforce coding standards (PHPCS)
  - Identify and fix code quality issues
  - Maintain quality throughout refactoring

- ✅ **AI-Assisted Development**
  - Delegate repetitive testing workflows to AI
  - Use AI for systematic edge case discovery
  - Verify AI suggestions before accepting
  - Balance autonomy with human oversight

---

## 📈 Your Testing Journey

### What You Built

**Vacation Calculator Application** with:
- ✅ Core functionality (proportional days + seniority bonus)
- ✅ Corner case handling (negative values, edge cases)
- ✅ Carryover feature (unused days from previous year)
- ✅ Full test coverage (36 tests: 25 unit + 11 E2E)

### Test Coverage Evolution

| Phase | Unit Tests | E2E Tests | Total | Pass Rate |
|-------|-----------|-----------|-------|-----------|
| **Workshop Start** | 10 | 0 | 10 | 100% |
| **After Part 1** | 21 | 0 | 21 | 100% |
| **After Part 3 (RED)** | 23 | 0 | 23 | 91% (2 failing) |
| **After Part 4 (GREEN)** | 23 | 0 | 23 | 100% |
| **After Part 5 (REFACTOR)** | 25 | 0 | 25 | 100% |
| **After Part 7 (E2E RED)** | 25 | 6 | 31 | 84% (5 failing) |
| **After Part 8 (E2E GREEN)** | 25 | 11 | **36** | **100%** 🎉 |

---

## 🎓 Key Takeaways

### What Makes Safe AI-Assisted Testing

✅ **DO THIS:**
- Use MCP tools for structured test execution
- Write tests BEFORE implementation (TDD)
- Run tests frequently to catch regressions early
- Verify AI suggestions with actual test runs
- Keep tests simple and focused
- Use static analysis for code quality
- Let AI handle repetitive tasks (test execution, result analysis)

### What Breaks Safe Testing

❌ **AVOID THIS:**
- Changing test expectations instead of fixing code
- Skipping test execution to "save time"
- Over-engineering simple solutions
- Writing tests after implementation
- Ignoring failing tests
- Trusting AI blindly without verification
- Manual bash commands instead of MCP tools

---

## 🔄 The Complete TDD Workflow

You've mastered the full cycle:

```
┌─────────────────────────────────────────────┐
│         Test-Driven Development             │
└─────────────────────────────────────────────┘

1. 🔴 RED PHASE
   ├─ Analyze requirements
   ├─ Write failing tests
   ├─ Run tests (verify they fail!)
   └─ Document what needs implementation

2. 🟢 GREEN PHASE
   ├─ Write MINIMAL code
   ├─ Make tests pass
   ├─ Run tests (verify they pass!)
   └─ No refactoring yet!

3. 🔵 REFACTOR PHASE
   ├─ Improve code quality
   ├─ Extract constants/magic numbers
   ├─ Run static analysis
   ├─ Run tests after each change
   └─ Keep all tests green!

4. ↻ REPEAT
   └─ Back to RED for next feature
```

**You did this TWICE** - once for unit tests (Parts 3-5), once for E2E tests (Parts 7-8)!

---

## 💡 Reflection Questions

Take a moment to reflect on your experience:

### Technical Growth
1. How has your confidence in testing changed?
2. What surprised you most about the TDD process?
3. Which phase (RED/GREEN/REFACTOR) was most valuable?
4. How did E2E tests differ from unit tests in practice?

### AI Collaboration
5. When did AI save you the most time?
6. When did you need to correct or guide the AI?
7. What testing tasks would you delegate to AI in the future?
8. What tasks still require human judgment?

### Real-World Application
9. Would you use TDD in your production projects?
10. What challenges do you foresee implementing this workflow?
11. How would you convince your team to adopt these practices?
12. What's your biggest takeaway from this workshop?

---

## 🚀 BONUS: Automating TDD with Skills

**This is where the workshop gets really powerful!**

You've been manually running tests and following TDD phases. But what if you could **automate the entire workflow** with reusable AI skills?

---

## Part 9 (BONUS): Creating Reusable TDD Skills

**⏱️ Time: ~20-30 minutes**

---

### 🎯 What You'll Learn

In this bonus section, you'll create **Claude Code skills** that automate common TDD workflows. Skills are reusable commands that teach AI to execute complex processes autonomously.

### Why Skills Matter

Throughout this workshop, you've repeatedly:
- Reminded AI to use MCP tools (not bash)
- Explained RED/GREEN/REFACTOR phases
- Verified test execution methods
- Checked for proper paths

**Skills eliminate this repetition!** Create the skill once, use it forever.

---

## 🧩 Skills You'll Create

### 1. `/run_unit_tests` - Execute PHPUnit via MCP

**Problem**: AI often defaults to bash commands instead of MCP tools.

**Solution**: A skill that ALWAYS uses MCP PHPUnit with correct paths.

#### 📝 Prompt Template

```
Create a Claude Code skill called "run_unit_tests" that:

1. ALWAYS uses MCP PHPUnit tools (never bash)
2. Accepts optional path parameter (relative, not absolute)
3. Accepts optional --filter parameter for specific tests
4. Analyzes results and explains failures
5. Provides clear next steps based on results

Requirements:
- Use mcp__phpunit__run_tests for execution
- Handle both "all tests" and "specific test file" scenarios
- Distinguish between RED PHASE failures (expected) and regressions
- Show test count and pass rate
- List failing tests with clear error messages

Save to: .claude/skills/run_unit_tests.md
```

### 🤔 What to Expect

AI should:
1. Read existing skill documentation (if any)
2. Create `.claude/skills/` directory if needed
3. Write comprehensive skill definition
4. Include usage examples
5. Document parameters and behavior

### ✅ Success Criteria

- [ ] Skill file created at `.claude/skills/run_unit_tests.md`
- [ ] Skill has clear description and purpose
- [ ] Explains WHY it exists (avoid bash, use MCP)
- [ ] Includes parameter documentation
- [ ] Shows usage examples
- [ ] Test the skill: `/run_unit_tests`

---

### 2. `/run_e2e_tests` - Execute Playwright Tests

**Problem**: E2E test execution is inconsistent and hard to interpret.

**Solution**: A skill that runs Playwright tests with proper analysis.

#### 📝 Prompt Template

```
Create a Claude Code skill called "run_e2e_tests" that:

1. Runs Playwright tests via npx playwright test
2. Accepts optional --grep parameter to filter tests
3. Accepts optional --project parameter (chromium/firefox/webkit)
4. Distinguishes RED PHASE failures from regressions
5. Provides detailed failure analysis with screenshots
6. Can use Playwright MCP for interactive debugging

Requirements:
- Run via bash: npx playwright test [options]
- Parse output to identify failing tests
- Show which UI elements are missing
- Link to HTML reports and screenshots
- Offer to use MCP Playwright for deeper investigation

Save to: .claude/skills/run_e2e_tests.md
```

### ✅ Success Criteria

- [ ] Skill file created at `.claude/skills/run_e2e_tests.md`
- [ ] Handles both full suite and filtered tests
- [ ] Distinguishes expected failures from regressions
- [ ] Shows clear failure reasons
- [ ] Test the skill: `/run_e2e_tests`

---

### 3. `/run_red_phase` - Automate RED Phase TDD

**Problem**: Constantly explaining TDD RED phase philosophy.

**Solution**: A skill that embodies RED phase thinking.

#### 📝 Prompt Template

```
Create a Claude Code skill called "run_red_phase" that:

1. Analyzes requirements for a new feature
2. Writes FAILING tests (unit and/or E2E)
3. Uses /run_unit_tests and /run_e2e_tests for execution
4. VERIFIES tests fail (this is success!)
5. Documents what needs implementation for GREEN phase
6. NEVER implements the feature (only tests!)

Requirements:
- Read CLAUDE.md to understand project structure
- Write clear test documentation
- Use helper skills (/run_unit_tests, /run_e2e_tests)
- Explain WHY tests fail
- Provide implementation roadmap

Parameters:
- Feature description (required)

Save to: .claude/skills/run_red_phase.md
```

### 🤔 Expected Behavior

When you run `/run_red_phase Add holiday tracking`:

1. ✅ Reads project config
2. ✅ Analyzes requirements
3. ✅ Writes unit tests (7 tests)
4. ✅ Runs `/run_unit_tests` → 7 failures ✅
5. ✅ Writes E2E tests (5 tests)
6. ✅ Runs `/run_e2e_tests` → 5 failures ✅
7. ✅ Documents implementation needed
8. ❌ Does NOT implement the feature!

### ✅ Success Criteria

- [ ] Skill created at `.claude/skills/run_red_phase.md`
- [ ] Uses `/run_unit_tests` and `/run_e2e_tests` internally
- [ ] Writes tests at both unit and E2E levels
- [ ] Verifies all new tests FAIL
- [ ] Existing tests still PASS (no regressions)
- [ ] Provides clear GREEN phase roadmap
- [ ] Test: `/run_red_phase Add employee overtime tracking`

---

### 4. `/run_green_phase` - Automate GREEN Phase TDD

**Challenge**: Create this one yourself!

#### 💪 Your Task

Based on the RED phase skill, create a GREEN phase skill that:
- Implements MINIMAL code to pass failing tests
- Runs tests after implementation
- Verifies ALL tests pass (new + existing)
- Does NOT refactor (keep it simple!)
- Provides REFACTOR phase suggestions

#### 📝 Prompt Template (Fill in the details!)

```
Create a Claude Code skill called "run_green_phase" that:

[YOUR REQUIREMENTS HERE]

Save to: .claude/skills/run_green_phase.md
```

**Hint**: GREEN phase is opposite of RED:
- RED: Write tests, verify they fail
- GREEN: Write code, verify tests pass

---

### 5. `/run_refactor_phase` - Automate REFACTOR Phase

**Challenge**: Create this one yourself too!

#### 💪 Your Task

Create a skill that:
- Runs static analysis (PHPStan, PHPCS)
- Fixes quality issues
- Extracts magic numbers to constants
- Improves naming and documentation
- **Runs tests after EACH change**
- Ensures tests stay GREEN

---

### 6. `/run_tdd_cycle` - Complete Automation

**Ultimate Challenge**: Combine all three phases!

#### 📝 Prompt Template

```
Create a Claude Code skill called "run_tdd_cycle" that:

1. Executes /run_red_phase [feature]
2. Executes /run_green_phase
3. Executes /run_refactor_phase
4. Provides complete summary

This is the FULL TDD workflow automated!

Save to: .claude/skills/run_tdd_cycle.md
```

---

## 🎯 Testing Your Skills

Once you've created the skills, test them:

### Test 1: Unit Tests
```
/run_unit_tests tests/TestCase/Service/VacationCalculatorTest.php
```

**Expected**: Uses MCP PHPUnit, shows results, analyzes failures.

### Test 2: E2E Tests
```
/run_e2e_tests --grep "Carryover Feature"
```

**Expected**: Runs Playwright, shows only carryover tests.

### Test 3: RED Phase (Holiday Feature)
```
/run_red_phase Add holiday tracking feature - exclude public holidays from vacation days
```

**Expected**:
- Writes 7 unit tests → all fail ✅
- Writes 5 E2E tests → all fail ✅
- Documents implementation needed
- Does NOT implement code

### Test 4: GREEN Phase
```
/run_green_phase
```

**Expected**:
- Implements `calculateVacationDaysUsed()` method
- Adds holiday form fields
- Runs tests → all pass ✅

### Test 5: REFACTOR Phase
```
/run_refactor_phase
```

**Expected**:
- Runs PHPStan → fixes issues
- Extracts holiday array to constant
- Improves docblocks
- Tests stay GREEN ✅

### Test 6: Complete TDD Cycle
```
/run_tdd_cycle Add weekend exclusion feature
```

**Expected**: Executes RED → GREEN → REFACTOR automatically!

---

## 📊 Skills vs Manual Workflow Comparison

| Aspect | Manual (Parts 1-8) | With Skills (Part 9) |
|--------|-------------------|---------------------|
| **Instructions needed** | Explain TDD every time | Explain once in skill |
| **Test execution** | Remind to use MCP | Automatic MCP usage |
| **Path errors** | Frequent mistakes | No path issues |
| **Consistency** | Varies per request | Always the same |
| **Speed** | Slow (lots of explanation) | Fast (just run skill) |
| **Reusability** | Copy/paste old prompts | `/run_red_phase` |
| **Error rate** | Medium (AI forgets) | Low (encoded in skill) |
| **Learning curve** | Steep (repeat concepts) | Shallow (skill knows it) |

---

## 🎓 What You Learned (BONUS Section)

### Skills Are Powerful

- ✅ **Encode best practices** - Write once, use forever
- ✅ **Eliminate repetition** - No more reminder prompts
- ✅ **Ensure consistency** - Same behavior every time
- ✅ **Compose workflows** - Skills call other skills
- ✅ **Share knowledge** - Team members use same skills

### When to Create Skills

Create a skill when you:
- Repeat the same instructions 3+ times
- Need AI to follow a specific process
- Want consistent tool usage (MCP vs bash)
- Build complex multi-step workflows
- Teach AI project-specific conventions

### Skill Design Principles

Good skills:
- Have clear, specific purpose
- Include why they exist (context)
- Document parameters and usage
- Show examples
- Compose with other skills
- Handle edge cases

---

## 🚀 Next Steps

### Immediate Actions

1. **Review your skills** - Test each one, fix issues
2. **Create project-specific skills** - For your own projects
3. **Share with your team** - `.claude/skills/` in git
4. **Iterate** - Improve skills based on usage

### Apply to Your Projects

- Set up MCP servers (PHPUnit, Playwright, etc.)
- Create skills for your testing workflows
- Use TDD for new features
- Let AI handle repetitive testing tasks
- Focus your time on design and architecture

### Continue Learning

- Explore other MCP servers (database, API testing, etc.)
- Create skills for code review workflows
- Automate deployment testing
- Build project-specific skills library

---

## 💬 Final Reflection

### You've Completed

- ✅ 8 core workshop parts
- ✅ 1 bonus section on skills
- ✅ Full TDD cycle (RED → GREEN → REFACTOR)
- ✅ Two testing levels (Unit + E2E)
- ✅ Multiple MCP integrations
- ✅ Reusable automation skills

### The Big Picture

**Before this workshop**: Testing was manual, tedious, and error-prone.

**After this workshop**: AI handles testing workflows autonomously using MCP tools and reusable skills.

**The transformation**:
```
❌ "Claude, run the tests"
   → Runs wrong command, bash instead of MCP

✅ "/run_unit_tests"
   → Perfect execution every time
```

---

## 🎊 You Did It! Skills Mastery Unlocked

You've not only learned AI-assisted testing - you've **automated the entire TDD workflow**. You can now:

- Write tests before code (TDD)
- Execute tests via MCP (PHPUnit + Playwright)
- Catch regressions instantly
- Refactor fearlessly
- Automate repetitive workflows with skills
- Build high-quality software faster

**This is the future of development** - AI as your testing partner, not just a code generator.

---

## 🌟 Share Your Success

**Share what you've learned**:
- Blog post about your TDD journey
- Demo video of skills in action
- GitHub repo with your skills
- Workshop feedback and improvements

**Connect with others**:
- Join Claude Code community
- Share your skills library
- Help others learn TDD + AI

---

## 📚 Resources

### MCP Documentation
- [MCP Protocol Spec](https://spec.modelcontextprotocol.io/)
- [PHPUnit MCP Server](https://github.com/ikloster03/mcp-server-phpunit)
- [Playwright MCP Server](https://github.com/microsoft/playwright)

### TDD Resources
- [Test-Driven Development by Kent Beck](https://www.amazon.com/Test-Driven-Development-Kent-Beck/dp/0321146530)
- [Growing Object-Oriented Software, Guided by Tests](https://www.amazon.com/Growing-Object-Oriented-Software-Guided-Tests/dp/0321503627)

### Claude Code Skills
- [Skills Documentation](https://docs.claude.com/claude-code/skills)
- [Example Skills Repository](https://github.com/anthropics/claude-code-examples)

---

## ✨ Thank You!

Thank you for completing Workshop 5! Your dedication to learning AI-assisted testing will make you a more effective developer.

**Remember**: The goal isn't to write more tests - it's to write better software with confidence.

---

**Workshop 5 Complete!** 🎊
**Total Time**: ~3-4 hours (including bonus)
**Skills Mastered**: TDD, MCP, AI Collaboration, Test Automation
**Tests Written**: 36+ (and counting!)

---
