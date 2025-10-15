# Workbook 01: Migration Fundamentals - User Plugin

## Introduction

Welcome to the hands-on workshop on migrating CakePHP 3 to CakePHP 5 with AI assistance!

In this workbook, you'll learn **two fundamental principles of safe AI-assisted migration**:

1. **The Smallest Change Principle** - make the smallest possible changes
2. **The Review-First Principle** - always analyze AI suggestions before implementing them

### Why Does This Matter?

We'll work with a real password recovery method in the User plugin - functionality that millions of users depend on. This demonstrates why a careful approach is critical.

## Learning Objectives

By the end of this workbook, you will be able to:

- Identify **one specific deprecated method** for migration (instead of trying to migrate everything at once)
- **Analyze AI suggestions** before applying them
- Add **critical business context** that AI cannot know
- **Control the scope of changes** - modify only what's necessary
- **Immediately verify** critical functions after changes
- Pay special attention to **security** when working with authentication code

## Key Concepts

### 🎯 The Smallest Change Principle

**The Right Approach:**
- ✅ Change 2 lines of code
- ✅ Modify 1 method
- ✅ Work with an isolated function

**The Wrong Approach:**
- ❌ "Migrate the entire authentication system"
- ❌ "Update the entire User controller"
- ❌ "Fix all deprecated request methods"

### 🔍 The Review-First Principle

**The Right Approach:**
- ✅ Analyze security implications
- ✅ Add business context that AI doesn't know
- ✅ Question AI assumptions about risks
- ✅ Review the diff before applying changes

**The Wrong Approach:**
- ❌ Auto-accept changes to security code
- ❌ Apply changes without understanding their impact
- ❌ Trust AI on business logic without verification

## Target Task

**CakePHP 3 File (Source)**: `quickapps-cakephp3/src/vendor/quickapps-plugins/user/src/Controller/GatewayController.php`

**CakePHP 5 File (Target)**: `quickapps-cakephp5/src/Controller/UserGatewayController.php` (we'll create this)

**Method**: `forgot()` (lines 38-50+)

**Change**: Update deprecated CakePHP 3 syntax for request data handling:
- `$this->request->data` → `$this->request->getData()`

**Approach**: We'll copy the source file from CakePHP 3 and migrate it to CakePHP 5 syntax

## Required Resources

Before starting, ensure you have access to:

- `migration-docs/CakePHP_3_TO_5_GUIDE.md` - breaking changes reference
- `migration-docs/CONTROLLERS_DEPENDENCY_MAP.md` - controller dependencies
- `migration-docs/unknown_patterns/` - QuickAppsCMS custom patterns
- `migration-docs/API.md` - authentication endpoints documentation

## Prerequisites

### Environment Setup

> **Note**: If you haven't set up the environments yet, complete [00-setup.md](./00-setup.md) first.

Make sure both CakePHP 3 and CakePHP 5 environments are running:
- CakePHP 3: http://localhost:8080
- CakePHP 5: http://localhost:8090

### Prepare the Workspace

We'll create a new controller by copying from CakePHP 3 source:

```bash
# View the CakePHP 3 source (for reference)
cat quickapps-cakephp3/src/vendor/quickapps-plugins/user/src/Controller/GatewayController.php

# We'll create UserGatewayController.php in CakePHP 5 at:
# quickapps-cakephp5/src/Controller/UserGatewayController.php
```

---

## ⏱️ Estimated Time: 30-40 minutes

In the following sections, we'll walk through the step-by-step process of migrating the `forgot()` method using safe AI-assisted practices.

---

## Part 1: Identifying the Migration Target

**⏱️ Time: ~5 minutes**

### Objective

Learn to find ONE specific deprecated method that needs migration, instead of trying to migrate everything at once.

### Key Principle

> 🎯 **Never migrate everything at once.** Start with a single, isolated deprecated method that won't cascade changes across the codebase.

### Your Task

Ask your AI assistant to find a single deprecated CakePHP 3 method in the `GatewayController.php` file.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Looking at quickapps-cakephp3/src/vendor/quickapps-plugins/user/src/Controller/GatewayController.php

I want to create a migrated version of this controller at:
quickapps-cakephp5/src/Controller/UserGatewayController.php

First, identify deprecated CakePHP 3 patterns that need migration to CakePHP 5.
Focus ONLY on methods that won't affect other parts of the codebase.
Point to the specific method and line number.

Reference method migration map in migration-docs/CakePHP_3_TO_5_GUIDE.md for deprecated patterns.

Start with the forgot() method - what deprecated patterns do you see?
```

### 🤔 What to Expect

The AI should identify:
- **Location**: `forgot()` method around lines 38-50
- **Deprecated pattern**: `$this->request->data`
- **Lines affected**: Approximately lines 40 and 44
- **Migration needed**: Change to `$this->request->getData()`

### ✅ Success Criteria

You've completed this step when:
- [ ] AI has identified the specific method (`forgot()`)
- [ ] AI has pointed to exact line numbers (40, 44)
- [ ] AI has explained what pattern is deprecated (`$this->request->data`)
- [ ] AI has suggested the modern replacement (`$this->request->getData()`)

### 🚨 Red Flags

Stop and reconsider if the AI suggests:
- ❌ "Migrate the entire controller"
- ❌ "Update all request methods across the project"
- ❌ "Fix multiple methods at once"

**Remember**: We want to change as little as possible for this first step.

### 📊 What You Learned

- How to scope migration work to the smallest possible unit
- How to reference migration documentation for deprecated patterns
- How to identify isolated changes that minimize risk

---

## Part 2: Requesting Analysis (Don't Implement Yet!)

**⏱️ Time: ~8 minutes**

### Objective

Learn to request a COMPLETE analysis from AI before implementing any changes. This is the foundation of the Review-First Principle.

### Key Principle

> 🔍 **Review BEFORE implementing.** Never let AI make changes until you fully understand what will happen and why.

### Your Task

Ask your AI assistant to analyze the `forgot()` method WITHOUT making any changes yet.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Let's work on the forgot() method. Provide a COMPLETE analysis WITHOUT making any changes:

1. What exactly needs to change and why?
2. What dependencies will be affected?
3. What are the risks if we implement this?
4. What should we test after the change?
5. Show me BEFORE and AFTER code side by side

DO NOT implement anything yet - just analyze and plan.
Reference migration-docs/CONTROLLERS_DEPENDENCY_MAP.md for dependencies.
```

### 🤔 What to Expect

The AI should provide:

#### 1. Change Explanation
- Migration from `$this->request->data` to `$this->request->getData()`
- Why: CakePHP 5 removed direct property access in favor of getter methods
- Affects 2 lines in the `forgot()` method

#### 2. Dependency Analysis
- Method is part of password recovery flow
- Uses `$this->request->data` to access POST data
- No other controllers depend on this specific method

#### 3. Risk Assessment
- Low technical risk (simple method change)
- Should mention this is a public endpoint
- May note authentication/security context

#### 4. Testing Requirements
- Test password reset form submission
- Verify data is correctly retrieved from POST
- Check both valid and invalid inputs

#### 5. Before/After Comparison
Something like:
```php
// BEFORE (CakePHP 3)
if (isset($this->request->data['username'])) {
    $username = $this->request->data['username'];
}

// AFTER (CakePHP 5)
if (isset($this->request->getData('username'))) {
    $username = $this->request->getData('username');
}
```

### ✅ Success Criteria

You've completed this step when:
- [ ] AI has explained the change without implementing it
- [ ] AI has identified which lines need to change
- [ ] AI has shown before/after code comparison
- [ ] AI has listed potential risks
- [ ] AI has suggested what to test
- [ ] **IMPORTANT**: No actual code changes have been made yet!

### 🚨 Red Flags

Stop immediately if the AI:
- ❌ Makes changes before you review the analysis
- ❌ Suggests changing more than just the `forgot()` method
- ❌ Doesn't explain security implications
- ❌ Skips showing before/after comparison

### 💡 Pro Tip

If the AI's analysis seems incomplete, ask follow-up questions:
- "What about security implications for this endpoint?"
- "Are there any edge cases we should consider?"
- "What happens if the data is malformed?"

### 📊 What You Learned

- How to request comprehensive analysis before implementation
- How to evaluate AI's understanding of the change
- How to identify gaps in AI's reasoning
- The importance of seeing before/after comparisons

---

## Part 3: Adding Human Context

**⏱️ Time: ~5 minutes**

### Objective

Learn to add critical business context that AI cannot know. This transforms generic technical analysis into real-world production planning.

### Key Principle

> 🧠 **AI doesn't know your business context.** It can analyze code, but YOU must add information about users, scale, security requirements, and business impact.

### Why This Matters

AI might see this as "just changing 2 lines of code." But YOU know:
- How many users depend on this feature
- What happens if it breaks
- Security implications for your specific environment
- Business constraints and deadlines

### Your Task

Review the AI's analysis from Part 2 and add YOUR critical context about this password recovery feature.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Good analysis, but let me add critical human context you missed:

This forgot() method handles PASSWORD RESET requests for 50,000+ active users.
Security implications you didn't consider:
- This endpoint is exposed to the public internet
- Brute force attacks target password reset functionality
- Rate limiting depends on proper request data parsing
- Failed migrations could lock users out of their accounts

Given this context, provide an updated plan focusing on SECURITY and USER ACCESS.
What additional precautions should we take?
```

### 🤔 What to Expect

After you provide this context, the AI should:

#### 1. Acknowledge the Scale
- Recognize the 50,000+ user impact
- Understand this is production-critical code
- Adjust risk assessment from "low" to "medium-high"

#### 2. Revise Security Analysis
- Emphasize testing with malformed input
- Suggest checking rate limiting behavior
- Recommend monitoring after deployment
- Propose rollback strategy

#### 3. Recommend Additional Testing
- Test with attack patterns (SQL injection attempts, XSS)
- Verify rate limiting still works correctly
- Test with various input formats
- Check error handling doesn't leak information

#### 4. Suggest Deployment Strategy
- Deploy during low-traffic period
- Monitor password reset success rates
- Keep rollback ready
- Test in staging with production-like data first

### ✅ Success Criteria

You've completed this step when:
- [ ] AI has acknowledged the business context you provided
- [ ] AI has revised the risk assessment upward
- [ ] AI has suggested additional security-focused tests
- [ ] AI has recommended deployment precautions
- [ ] AI's response shows it understands this is production-critical

### 🚨 Red Flags

Be concerned if the AI:
- ❌ Dismisses your context as unnecessary
- ❌ Maintains "this is a simple change" assessment
- ❌ Doesn't update testing recommendations
- ❌ Rushes to implementation without addressing concerns

### 💡 Real-World Examples of Human Context

Here are other examples of context YOU must provide:

**Scale Context:**
- "This runs 1 million times per day"
- "We have 5 minutes downtime budget per month"
- "This feature generates 40% of our revenue"

**Security Context:**
- "We're PCI compliant and this touches payment data"
- "This endpoint has been targeted by attackers before"
- "We're subject to GDPR/HIPAA regulations"

**Business Context:**
- "Marketing campaign launches tomorrow using this feature"
- "Support team isn't available on weekends"
- "CEO demos this feature to investors next week"

### 📊 What You Learned

- AI analyzes code structure, but YOU provide business impact
- Security context changes risk assessment dramatically
- Human knowledge transforms "simple changes" into careful production planning
- Your domain expertise is irreplaceable

---

## Part 4: Guided Implementation

**⏱️ Time: ~7 minutes**

### Objective

Learn to implement changes with strict human oversight and control. You decide exactly what gets changed - the AI executes your instructions.

### Key Principle

> 🎯 **You control the scope, AI executes.** Always specify exact constraints and review changes before they're applied.

### Your Task

Now that you've analyzed and added context, it's time to implement - but with precise boundaries.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Now let's create the migrated controller file. Follow these steps:

1. Copy the source file from quickapps-cakephp3/src/vendor/quickapps-plugins/user/src/Controller/GatewayController.php
2. Create a new file at quickapps-cakephp5/src/Controller/UserGatewayController.php
3. In the new file, migrate ONLY the forgot() method:
   - Change ONLY the lines that use $this->request->data to $this->request->getData()
   - Preserve exact same functionality and security checks
   - Update the namespace to App\Controller
   - Rename the class to UserGatewayController

4. Show me the diff of what will change in the forgot() method BEFORE creating the file

Constraints:
- Change ONLY request->data lines in forgot() method - nothing else
- If you suggest any other changes, I'll stop you

CRITICAL: This handles user authentication - I must review the exact changes first.
```

### 🤔 What to Expect

The AI should:

#### 1. Show You the Exact Changes First

Something like:
```diff
- if (isset($this->request->data['username'])) {
-     $username = $this->request->data['username'];
+ if (isset($this->request->getData('username'))) {
+     $username = $this->request->getData('username');
```

#### 2. Wait for Your Approval

The AI should NOT apply changes immediately. It should:
- Show the diff
- Explain what will change
- Ask for your confirmation

#### 3. Apply Only What You Approved

After you approve, it should change:
- ✅ Only the specified lines
- ✅ Only in the `forgot()` method
- ✅ Nothing else

### ✅ Success Criteria

You've completed this step when:
- [ ] AI showed you the diff BEFORE making changes
- [ ] You reviewed and understood every line that will change
- [ ] AI applied ONLY the changes you approved
- [ ] No unexpected files were modified
- [ ] The changes match exactly what was in the diff

### 🚨 Red Flags

Stop immediately if the AI:
- ❌ Makes changes without showing you the diff first
- ❌ Modifies other methods "while we're at it"
- ❌ Changes more lines than necessary
- ❌ Adds "improvements" you didn't ask for
- ❌ Touches other files

### 💡 Review Checklist

Before approving the changes, verify:

**Scope Check:**
- [ ] Only `forgot()` method affected?
- [ ] Only request data access changed?
- [ ] No other functionality modified?

**Security Check:**
- [ ] Authentication logic unchanged?
- [ ] Validation logic unchanged?
- [ ] Error handling unchanged?

**Functionality Check:**
- [ ] Same data being accessed?
- [ ] Same conditions being checked?
- [ ] Same behavior expected?

### 🔍 What If You Spot a Problem?

If you see something wrong in the diff, **don't approve it**. Instead, say:

```
Wait - I see a problem with [specific issue].
Let's revise the approach to [your suggestion].
Show me the updated diff.
```

### 📊 What You Learned

- How to set strict boundaries for AI implementation
- The importance of reviewing diffs before applying changes
- How to maintain control over what gets changed
- Why "show me first" protects production code

---

## Part 5: Immediate Verification

**⏱️ Time: ~5 minutes**

### Objective

Learn to immediately verify that your changes are scoped correctly and understand what testing would be needed.

### Key Principle

> ✅ **Verify scope control worked.** Always check that ONLY what you intended changed, and understand what needs testing.

### Your Task

Verify the migration was done correctly and create a basic test as an example.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Now let's verify our authentication change:

1. Show git diff - confirm ONLY forgot() method changed
2. Create a unit test for the method
3. Show what the test would verify (username and email input handling)

The change should be minimal and preserve exact authentication behavior.
```

### 🤔 What to Expect

The AI should:

#### 1. Show Git Diff

```diff
diff --git a/.../UserGatewayController.php b/.../UserGatewayController.php
@@ -40,2 +40,2 @@
-    if (isset($this->request->data['username'])) {
+    if (isset($this->request->getData('username'))) {
```

#### 2. Create Example Unit Test

Something like:
```php
public function testForgotWithValidUsername()
{
    $this->post('/user-gateway/forgot', ['username' => 'testuser']);
    $this->assertResponseOk();
}

public function testForgotWithValidEmail()
{
    $this->post('/user-gateway/forgot', ['username' => 'test@example.com']);
    $this->assertResponseOk();
}
```

#### 3. Explain What Should Be Tested

- Valid username input is processed correctly
- Valid email input is processed correctly
- Both request data patterns we changed work as expected

### ✅ Success Criteria

You've completed this step when:
- [ ] Git diff shows ONLY the forgot() method changed
- [ ] Only request->data lines were modified
- [ ] AI created example test code
- [ ] You understand what aspects need testing
- [ ] **Note**: This is a demonstration - production would need full test suite

### 📊 What You Learned

- How to verify scope control worked (git diff)
- What kind of tests authentication code needs
- The importance of immediate verification
- How to check that minimal changes were made

> **Important**: This workbook demonstrates the PROCESS of safe migration. In a real production scenario, you would run the full test suite, check database integration, and do comprehensive testing before deployment.

---

## 🎉 Congratulations!

You've successfully completed your first AI-assisted migration using the two fundamental principles:

### 🎯 The Smallest Change Principle
You changed just 2 lines in 1 method - nothing more.

### 🔍 The Review-First Principle
You analyzed, added context, reviewed diffs, and verified before trusting.

### 🚀 What's Next?

Now that you understand the fundamentals, you can:

1. **Apply to other methods**: Use the same approach for other deprecated patterns in the GatewayController
2. **Expand to other controllers**: Migrate other controllers following the same principles
3. **Learn advanced patterns**: Move to the next workbook for more complex migration scenarios

### 📝 Key Takeaways

**What Makes Safe AI-Assisted Migration:**
- ✅ Small, isolated changes
- ✅ Analysis before implementation
- ✅ Human context and oversight
- ✅ Strict scope control
- ✅ Immediate verification

**What Breaks AI-Assisted Migration:**
- ❌ "Migrate everything at once"
- ❌ Auto-accepting suggestions
- ❌ Skipping context and analysis
- ❌ No verification or testing
- ❌ Trusting AI without review

### 💬 Reflection Questions

1. How did adding business context change the AI's approach?
2. What would have happened if you skipped the verification step?
3. How does this approach compare to manual migration?
4. What will you do differently in your next migration?

---

**Remember**: Migration isn't about speed - it's about preserving user trust while modernizing safely.

**Next Workbook**: [02-continuous-testing-loops.md](./02-continuous-testing-loops.md) - Learn to maintain quality through iterative testing →
