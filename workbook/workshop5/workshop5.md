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

- **workshop5-1**: Introduction + Testing Basics
- **workshop5-2**: Static Analysis
- **workshop5-3**: Debugging Test Failures
- **workshop5-4**: Advanced Testing Workflows
- **workshop5-final**: Complete reference with all sections

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

Now that your environment is verified, let's begin the workshop!

### Next Steps

Switch to branch `workshop5-1` to start learning about AI-assisted testing:

```bash
git checkout workshop5-1
```

Then open this file again - you'll see new content has been added!

---

**⏱️ Time for warm-up**: ~5 minutes
**Total workshop time**: ~45 minutes

---

> **Note**: This workshop demonstrates practical AI workflows for testing. While we use a real CakePHP project, the principles apply to any technology stack.
