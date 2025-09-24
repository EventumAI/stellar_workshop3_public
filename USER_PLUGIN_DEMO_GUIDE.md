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

Find ONE deprecated CakePHP 3 method that needs migration to CakePHP 5.
Focus ONLY on methods that won't affect other parts of the codebase.
Point to the specific line number.

Reference migration-docs/CakePHP_3_TO_5_GUIDE.md for deprecated patterns.
```

**Expected AI Response:** Will identify `$this->request->data` on line 40 and 44 in the `forgot()` method.

### Step 2: Request Analysis (Don't Implement Yet!) (3 minutes)

**SAY:** "Second principle: Review BEFORE implementing. Let's analyze what AI suggests. Notice I'm NOT asking it to fix anything yet."

**PROMPT 2A - Analysis Request:**
```
For the deprecated request->data usage in the forgot() method, provide a COMPLETE analysis WITHOUT making any changes:

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

Adjust your migration approach considering:
1. Zero-downtime deployment (users can't lose access)
2. Backward compatibility with existing password reset emails
3. Security implications if request parsing breaks
4. Rollback strategy if authentication breaks

Updated plan please, focusing on SECURITY and USER ACCESS.
```

**Expected AI Response:** Will revise approach to emphasize security testing and gradual rollout.

### Step 4: Implement with Human Oversight (3 minutes)

**SAY:** "Now we implement, but with constant human verification. Notice how I control exactly what gets changed."

**PROMPT 4A - Guided Implementation:**
```
Implement ONLY the request->data migration in the forgot() method with these constraints:

1. Change ONLY lines 40 and 44 - nothing else
2. Add a comment: "// CakePHP 5 Migration - Security Reviewed [YOUR_DATE]"
3. Preserve exact same functionality and security checks
4. Show me each change before applying it

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
2. Create a test case for the password reset flow
3. Test with both username and email input (lines we changed)
4. Check for any security warnings or deprecations

The change should be minimal and preserve exact authentication behavior.
Test both: valid user requests and invalid user requests.
```

**Expected AI Response:** Will show git diff and create appropriate tests for password recovery.

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

---

## 💡 Key Takeaways (1.5 minutes)

**SAY THESE POINTS:**
1. **"Authentication = highest risk"** - When users can't log in, your business stops
2. **"Human context beats AI confidence"** - AI doesn't know your security requirements
3. **"Two-line change, 30-minute analysis"** - Security deserves extra time
4. **"Test auth changes immediately"** - Don't wait to discover login is broken

---

## ⚠️ Anti-Patterns to Avoid

**SHOW BRIEFLY:**
```
❌ "Migrate entire User controller"
❌ "Update all authentication at once"
❌ "Fix all deprecated request methods"
❌ Auto-accepting auth/security changes
```

---

## 🔒 Why User Plugin is Perfect for This Demo

**SAY:** "I chose password recovery because:"
- **Universal Understanding** - Everyone knows "forgot password"
- **Clear Security Stakes** - Broken auth = locked out users
- **Simple Technical Change** - Two lines, dramatic business impact
- **Real Migration Pattern** - `request->data` appears throughout CakePHP 3 codebases

---

## 🎬 Transition to Next Slide (30 seconds)

**SAY:** "Now that we understand how to make safe, small changes with human oversight, let's see how to maintain quality through continuous testing loops. Security changes like these need immediate validation..."

---

## 📚 Reference Files for Deeper Context

**For complete technical details:**
- **migration-docs/API.md** - Shows this endpoint serves `/user/gateway/forgot`
- **migration-docs/CONTROLLERS_DEPENDENCY_MAP.md** - UserSignTrait usage patterns
- **migration-docs/CakePHP_3_TO_5_GUIDE.md** - Complete request object migration guide
- **migration-docs/unknown_patterns/CUSTOM_AUTH_SYSTEM.md** - Authentication architectural decisions

---

## 🚨 Emergency Fallback

If technical issues occur:
1. Use backup screenshot of the `forgot()` method showing `$this->request->data`
2. Show prepared before/after code comparison on slides
3. Focus on principles: "Small scope = Small risk, Security = Extra care"
4. Use whiteboard: "AI Suggests → Human Reviews → Implement → Test"

---

## 🎯 Success Metrics

Demo succeeds when audience understands:
- ✅ **Scope Control**: Change only what's necessary
- ✅ **Human Oversight**: Add business context AI lacks
- ✅ **Security Focus**: Authentication deserves extra caution
- ✅ **Immediate Testing**: Verify critical functions work

**Key Message**: "Migration isn't about speed - it's about preserving user trust while modernizing safely."