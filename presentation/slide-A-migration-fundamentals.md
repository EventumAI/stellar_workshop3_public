# Slide A: Migration Fundamentals - User Plugin Demo
## Speaker Script & Demo Guide

### ⏱️ **TOTAL TIME: 15 minutes**
- Introduction: 1 minute
- Live Demo: 12 minutes (integrated flow)
- Key Points: 1.5 minutes
- Transition: 30 seconds

### 🎯 Slide Objective
**Merges**: Slide 1 (Smallest Change) + Slide 2 (Review-First Development)

Demonstrate the foundational human-in-the-loop principles: making smallest possible changes while reviewing AI suggestions before implementation.

### 🎯 **Demo Target: User Plugin - Password Recovery Method**
**File**: `quickapps-cakephp5/vendorCake3/quickapps-plugins/user/src/Controller/GatewayController.php`
**Method**: `forgot()` method (lines 38-50+)
**Change**: `$this->request->data` → `$this->request->getData()`

### 📚 Context Files Required
- `migration-docs/CakePHP_3_TO_5_GUIDE.md` - Breaking changes reference
- `migration-docs/CONTROLLERS_DEPENDENCY_MAP.md` - Controller dependencies
- `migration-docs/unknown_patterns/` - Custom patterns to watch for
- `migration-docs/API.md` - Authentication endpoints reference

### 📋 Pre-Demo Setup
```bash
# Start both environments
docker-compose -f quickapps-cakephp3/docker-compose.yml up -d
docker-compose -f quickapps-cakephp5/docker-compose.yml up -d

# Verify they're running
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080  # Should be 200
curl -s -o /dev/null -w "%{http_code}" http://localhost:8090  # Should be 200

# Open target file for demo
code quickapps-cakephp5/vendorCake3/quickapps-plugins/user/src/Controller/GatewayController.php
```

---

## 🎤 Speaker Introduction (1 minute)

**SAY:** "Today we'll learn the two most important principles for safe AI-assisted migration: making the SMALLEST possible changes and REVIEWING AI suggestions before implementing. We'll use a real password recovery method that millions of users depend on - this demonstrates why being careful matters."

---

## 💻 Integrated Demo Flow (12 minutes)

### Step 1: Identify Single Target (2 minutes)

**SAY:** "First principle: Never migrate everything at once. Let's find ONE deprecated method in authentication code."

**PROMPT 1A - Find Single Target:**
```
Looking at quickapps-cakephp5/vendorCake3/quickapps-plugins/user/src/Controller/GatewayController.php

Find deprecated CakePHP 3 method that needs migration to CakePHP 5.
Focus ONLY on methods that won't affect other parts of the codebase.
Point to the specific method and line number.

Reference migration-docs/CakePHP_3_TO_5_GUIDE.md for deprecated patterns.
```

**Expected AI Response:** Will identify `$this->request->data` on line 40 and 44 in the `forgot()` method.

### Step 2: Request Analysis (Don't Implement Yet!) (3 minutes)

**SAY:** "Second principle: Review BEFORE implementing. Let's analyze what AI suggests. Notice I'm NOT asking it to fix anything yet."

**PROMPT 2A - Analysis Request:**
```
Lets work on the forgot method, provide a COMPLETE analysis WITHOUT making any changes:

1. What exactly needs to change and why?
2. What dependencies will be affected?
3. What are the risks if we implement this?
4. What should we test after the change?
5. Show me BEFORE and AFTER code side by side

DO NOT implement anything yet - just analyze and plan.
Reference migration-docs/CONTROLLERS_DEPENDENCY_MAP.md for dependencies.
```

**Expected AI Response:** Will explain `$this->request->data` → `$this->request->getData()` migration with analysis.

### Step 3: Human Review and Correction (2 minutes)

**SAY:** "Now I review the AI's understanding and add critical human context it would never know."

**PROMPT 3A - Human Review:**
```
Good analysis, but let me add critical human context you missed:

This forgot() method handles PASSWORD RESET requests for 50,000+ active users.
Security implications you didn't consider:
- This endpoint is exposed to the public internet
- Brute force attacks target password reset functionality
- Rate limiting depends on proper request data parsing
- Failed migrations could lock users out of their accounts


Updated plan please, focusing on SECURITY and USER ACCESS.
```

**Expected AI Response:** Will revise approach to emphasize security testing and gradual rollout.

### Step 4: Implement with Human Oversight (3 minutes)

**SAY:** "Now we implement, but with constant human verification. Notice how I control exactly what gets changed."

**PROMPT 4A - Guided Implementation:**
```
Implement ONLY the request->data migration in the forgot() method with these constraints:

1. Change ONLY lines 40 and 44 - nothing else
2. Preserve exact same functionality and security checks
3. Show me each change before applying it

If you suggest anything beyond these two lines, I'll stop you.
CRITICAL: This handles user authentication - show me the diff first.
```

**Expected AI Response:** Will show specific line changes for review before implementing.

### Step 5: Immediate Verification (2 minutes)

**SAY:** "Never trust - always verify immediately. Authentication code that doesn't work means users can't access their accounts."

**PROMPT 5A - Verification:**
```
Now let's verify our authentication change:

1. Show git diff - confirm ONLY forgot() method changed
2. Create a unit tests for the method.
3. Test with both username and email input (lines we changed)

The change should be minimal and preserve exact authentication behavior.
Test both: valid user requests and invalid user requests.
```

**Expected AI Response:** Will show git diff and create appropriate tests for password recovery.


Now we can do the same for the other lines on the Controller.

**PROMPT 6A - Apply the same changes:**
```
Using the same approach migrate the other lines on GatewayController

```

---

## 📊 Core Principles Demonstrated

**SHOW ON SLIDE:**
```
🎯 SMALLEST CHANGE PRINCIPLE:
✅ Two lines only (40, 44)
✅ Single method modification
✅ Isolated authentication function
❌ Never "migrate entire auth system"

🔍 REVIEW-FIRST PRINCIPLE:
✅ Analyze security implications
✅ Add human business context
✅ Question AI assumptions about risk
❌ Never auto-accept auth changes
```

## ⚠️ Anti-Patterns to Avoid

**SHOW BRIEFLY:**
```
❌ "Migrate entire User controller"
❌ "Update all authentication at once"
❌ "Fix all deprecated request methods"
❌ Auto-accepting auth/security changes
```

---


## 🎬 Transition to Next Slide (30 seconds)

**SAY:** "Now that we understand how to make safe, small changes with human oversight, let's see how to maintain quality through continuous testing loops. Security changes like these need immediate validation..."

---

## 🎯 Success Metrics

Demo succeeds when audience understands:
- ✅ **Scope Control**: Change only what's necessary
- ✅ **Human Oversight**: Add business context AI lacks
- ✅ **Security Focus**: Authentication deserves extra caution
- ✅ **Immediate Testing**: Verify critical functions work

**Key Message**: "Migration isn't about speed - it's about preserving user trust while modernizing safely."