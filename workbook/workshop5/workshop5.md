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
