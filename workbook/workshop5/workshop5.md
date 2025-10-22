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

> **Note**: This workshop demonstrates practical AI workflows for testing. While we use a real CakePHP project, the principles apply to any technology stack.
