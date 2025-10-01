# Slide D: Component Migration Strategy - Complete Demo Guide
## Step-by-Step with Tested Prompts

**Branch**: `item4`
**Total Time**: 12 minutes
**Environment**: QuickAppsCMS CakePHP 3 → CakePHP 5 Migration

---

## 🎯 Slide Objecti
Demonstrate how to use Claude Code to break down complex CMS migrations into manageable units using natural boundaries (controllers & plugins). Show real workflows that attendees can replicate.

---

## 📚 Prerequisites - Files to Have Ready

```bash
# Documentation Files (already created)
migration-docs/CONTROLLERS_DEPENDENCY_MAP.md
migration-docs/PLUGINS_DEPENDENCY_MAP.md
migration-docs/DEPENDENCY_MATRIX.md
migration-docs/test-plans/TEST_PLAN_LOCALE.md

# Source Code Locations
quickapps-cakephp5/vendorCake3/quickapps-plugins/  # 17 plugins with 43 controllers
quickapps-cakephp5/plugins/                         # Target location for migrations

# Current Status
- 43 controllers identified
- 17 plugins mapped
- Controller hierarchy documented
```

---

## 📋 Pre-Demo Setup Checklist (Run 5 minutes before presentation)

```bash
# 1. Verify branch
git status
# Should show: On branch item4

# 2. Count controllers (should show 43)
find quickapps-cakephp5/vendorCake3/quickapps-plugins/ -name "*Controller.php" | wc -l

# 3. List plugins (should show 17 plugins)
ls -la quickapps-cakephp5/vendorCake3/quickapps-plugins/

# 4. Open key files in editor (optional but helpful)
# - migration-docs/CONTROLLERS_DEPENDENCY_MAP.md
# - quickapps-cakephp5/vendorCake3/quickapps-plugins/locale/src/Controller/Admin/ManageController.php

# 5. Have Claude Code ready in terminal
cd /Users/cadukiz/Desktop/dev/workshops/W3
```

---

## 🎤 PRESENTATION FLOW

### Introduction (1 minute)

**SAY:**
> "Complex migrations fail when we try to do everything at once. Let me show you how Claude Code helps us use natural boundaries - controllers and plugins - to break down a 17-plugin CMS migration into safe, testable units."

**SHOW (slide visual):**
```
QuickAppsCMS Migration Challenge:
├── 17 Core Plugins
├── 43 Controllers
├── Complex Dependencies
└── Need: Safe Migration Strategy
```

---

## 💻 LIVE DEMO PART 1: System Complexity Analysis (2.5 minutes)

### Step 1A: Understanding What We're Dealing With

**SAY:**
> "First, we need Claude to analyze our system complexity. I'll use a structured prompt that tells Claude exactly what to analyze."

**TYPE THIS PROMPT IN CLAUDE CODE:**

```
Analyze the QuickAppsCMS system complexity for migration planning:

PART 1 - Controller Analysis:
Examine quickapps-cakephp5/vendorCake3/quickapps-plugins/ and categorize all controllers by migration complexity:

1. Simple CRUD controllers (standard index/add/edit/delete)
   - Risk: LOW
   - Migration time: 1-2 hours each

2. Authentication/Security controllers (UserGatewayController, AuthComponent usage)
   - Risk: HIGH
   - Migration time: 1-2 days each

3. Complex business logic controllers (File handlers, EAV operations)
   - Risk: MEDIUM-HIGH
   - Migration time: 4-8 hours each

Count controllers in each category.

PART 2 - Plugin Dependency Analysis:
Using migration-docs/PLUGINS_DEPENDENCY_MAP.md and DEPENDENCY_MATRIX.md:

1. Identify foundation plugins (no dependencies or only depend on cms)
2. Find plugins with circular dependencies (if any)
3. Calculate dependency chain depth for each plugin

PART 3 - Migration Priority Matrix:
Create a matrix showing:
```
┌─────────────────────┬──────────────────────┬──────────────────────┐
│ Priority 1          │ Priority 2           │ Priority 3           │
├─────────────────────┼──────────────────────┼──────────────────────┤
│ Simple + No Deps    │ Simple + Foundation  │ Complex + Multi-Deps │
│ Example: locale     │ Example: menu        │ Example: content     │
└─────────────────────┴──────────────────────┴──────────────────────┘
```

Suggest the optimal starting point for our migration with justification.
```

**EXPECTED CLAUDE RESPONSE:**
Claude should:
1. Count 43 controllers and categorize them
2. Identify locale, captcha as low-dependency plugins
3. Flag content, user as high-complexity
4. Recommend starting with **locale plugin** (simple CRUD, minimal dependencies)

**SAY AFTER CLAUDE RESPONDS:**
> "Perfect! Claude identified that the Locale plugin is our ideal starting point - it's simple CRUD operations with minimal dependencies. This is exactly the kind of analysis that prevents us from starting with something complex like the Content plugin."

---

## 💻 LIVE DEMO PART 2: Controller Migration Demo (3 minutes)

### Step 2A: Demonstrating Controller-as-Boundary

**SAY:**
> "Now let's see how Claude helps us migrate a single controller as an isolated unit. This is the LocaleController's ManageController - it handles language CRUD operations."

**TYPE THIS PROMPT:**

```
Let's demonstrate controller-as-boundary migration using LocaleController:

STEP 1 - Pre-Migration Analysis:
Analyze quickapps-cakephp5/vendorCake3/quickapps-plugins/locale/src/Controller/Admin/ManageController.php

Create a comprehensive checklist identifying:

A. CakePHP 3 → 5 Breaking Changes Present:
   - $this->request->data vs $this->request->getData()
   - $this->request->data() vs $this->request->is('post')
   - Flash component method changes (Flash::danger → Flash::error)
   - redirect() return values
   - Model loading patterns

B. Dependencies to Update:
   - Parent class: AppController (needs CakePHP 5 update)
   - Components: Flash, Breadcrumb
   - Models: Languages, Options
   - Utility: LocaleToolbox

C. Business Logic to Preserve (CRITICAL):
   - Language ordering algorithm (move() method)
   - Default language switching validation
   - Status toggle logic with safeguards
   - Deletion protection for active languages

STEP 2 - Create Migration Checklist:
Format as a detailed checklist showing what needs updating.

STEP 3 - Estimate Migration Time:
Based on complexity, estimate hours needed for this controller.

DO NOT perform the migration yet - this is analysis only.
```

**EXPECTED CLAUDE RESPONSE:**
Claude should provide:
1. Detailed list of CakePHP 3→5 changes needed (8-10 changes)
2. Dependency update list
3. Business logic preservation notes
4. Time estimate (4-6 hours for this controller)

**SAY AFTER CLAUDE RESPONDS:**
> "Notice how Claude identified specific breaking changes like `$this->request->data` to `getData()`, and crucially, it preserved the business logic like the language ordering algorithm. This is the power of controller-as-boundary - complete feature isolation."

**SHOW CODE EXAMPLE (point to screen):**
```php
// CakePHP 3 (current)
if (!empty($this->request->data['code'])) {

// CakePHP 5 (needs migration)
if (!empty($this->request->getData('code'))) {
```

---

## 💻 LIVE DEMO PART 3: Plugin Isolation Strategy (2.5 minutes)

### Step 3A: Safe Plugin Migration Using Isolation

**SAY:**
> "For complex plugins, we use isolation testing to prevent breaking the entire system. Let me show you how Claude guides us through creating an isolated test environment."

**TYPE THIS PROMPT:**

```
Demonstrate safe plugin migration using isolation workspace:

OBJECTIVE: Show how to test the Locale plugin migration in isolation before integrating into main project.

STEP 1 - Explain Isolation Strategy:
Describe why we DON'T want to:
❌ Migrate plugin directly in main project
❌ Test in production-like environment initially
❌ Integrate before validation

Instead we:
✅ Create isolated CakePHP 5 environment
✅ Test plugin independently
✅ Validate all functionality
✅ Then integrate with confidence

STEP 2 - Create Isolation Workspace Plan:
Outline the commands needed to:

A. Create standalone CakePHP 5 test project:
```bash
composer create-project cakephp/app:~5.0 locale-migration-test
cd locale-migration-test
```

B. Copy ONLY the Locale plugin:
```bash
mkdir -p plugins/Locale
cp -r ../quickapps-cakephp5/vendorCake3/quickapps-plugins/locale/src \
     plugins/Locale/src
```

C. Install minimal dependencies:
```bash
# Add only what Locale needs (not all 17 plugins)
composer require cakephp/orm:^5.0
```

STEP 3 - Validation Checklist Before Reintegration:
```markdown
## Locale Plugin Isolation Testing

### Functionality Tests
- [ ] Plugin loads without errors
- [ ] Database tables accessible
- [ ] Language CRUD operations work
- [ ] Ordering algorithm functions correctly
- [ ] Default language switching validated

### Performance Tests
- [ ] Page load time < 200ms
- [ ] Database queries optimized
- [ ] No N+1 query issues

### Integration Tests (after isolated tests pass)
- [ ] Works with CMS core plugin
- [ ] Menu integration functional
- [ ] Admin interface renders correctly

### Migration Complete Only When All Checked
```

STEP 4 - Risk Mitigation:
Explain what we do if isolated tests FAIL vs PASS.

Reference: migration-docs/test-plans/TEST_PLAN_LOCALE.md for complete test scenarios.

DO NOT create the workspace - explain the strategy only.
```

**EXPECTED CLAUDE RESPONSE:**
Claude should:
1. Explain isolation benefits clearly
2. Provide bash commands for workspace setup
3. Create comprehensive validation checklist
4. Reference the existing test plan document
5. Explain rollback strategy

**SAY AFTER CLAUDE RESPONDS:**
> "This isolation strategy is crucial. If we had migrated the Locale plugin directly and it broke, we could affect the entire admin interface. By testing in isolation, we catch issues early and integrate with confidence."

**VISUAL AID (draw or show diagram):**
```
Isolated Testing Flow:
┌────────────────────────────────────────────────┐
│  1. Create Isolation Workspace                 │
│     ↓                                          │
│  2. Migrate Plugin Code                        │
│     ↓                                          │
│  3. Run All Tests in Isolation ← WE ARE HERE  │
│     ↓                                          │
│  4. [PASS] → Integrate to Main Project         │
│  4. [FAIL] → Fix Issues (main project safe!)   │
└────────────────────────────────────────────────┘
```

---

## 💻 LIVE DEMO PART 4: Dependency Resolution (1.5 minutes)

### Step 4A: Creating Migration Waves

**SAY:**
> "Dependencies determine migration order. Let's have Claude create migration waves based on our dependency analysis."

**TYPE THIS PROMPT:**

```
Create optimal migration wave sequence using dependency analysis:

Using:
- migration-docs/PLUGINS_DEPENDENCY_MAP.md (17 plugins, dependency graph)
- migration-docs/DEPENDENCY_MATRIX.md (detailed relationships)
- migration-docs/CONTROLLERS_DEPENDENCY_MAP.md (controller complexity)

Generate migration waves where:
- Each wave can be worked on in parallel
- No wave starts until previous wave is complete
- Dependencies are respected
- Risk is distributed across waves

Format as:

## WAVE 1 - Foundation (Week 1-2)
**Can Start Now - No Blockers**

| Plugin | Controllers | Depends On | Risk | Estimated Time |
|--------|-------------|------------|------|----------------|
| cms    | 1           | (none)     | 🔴 HIGH | 2 weeks |
| eav    | 0           | (none)     | 🟡 MEDIUM | 1 week |

**Why Wave 1**: Foundation plugins required by everyone else.

## WAVE 2 - Core Features (Week 3-4)
**Blocked Until**: Wave 1 complete

| Plugin | Controllers | Depends On | Risk | Estimated Time |
|--------|-------------|------------|------|----------------|
| locale | 1           | cms        | 🟢 LOW | 3 days |
| user   | 6           | cms, field | 🔴 HIGH | 2 weeks |

(Continue for all waves...)

At the end, show:

### Migration Timeline Summary
```
Wave 1: Weeks 1-2   (Foundation)
Wave 2: Weeks 3-4   (Core Features)
Wave 3: Weeks 5-6   (Content System)
Wave 4: Weeks 7-8   (Advanced Features)

Total Estimated Time: 8-10 weeks
```

### Critical Path
Identify the longest dependency chain that determines minimum project duration.

### Parallelization Opportunities
Show which plugins in each wave can be worked on simultaneously by multiple developers.
```

**EXPECTED CLAUDE RESPONSE:**
Claude should:
1. Create 4-5 migration waves
2. Show clear dependencies between waves
3. Provide timeline estimate (8-10 weeks)
4. Identify critical path (cms → field → content chain)
5. Show parallelization options

**SAY AFTER CLAUDE RESPONDS:**
> "This wave structure gives us a clear roadmap. Notice how locale and captcha can be migrated in parallel in Wave 2 - that's where multiple developers can work simultaneously. The critical path is cms → field → content, which determines our minimum timeline."

---

## 📊 Visual Summary Slide (Show for 1 minute)

**DISPLAY THIS SUMMARY:**

```
Component Migration Strategy Summary
═══════════════════════════════════════════════════════

1️⃣ ANALYZE COMPLEXITY
   ├─ 43 controllers categorized by risk
   ├─ 17 plugins mapped with dependencies
   └─ Priority matrix created

2️⃣ CONTROLLER-AS-BOUNDARY
   ├─ Isolated feature migration
   ├─ Breaking changes identified
   └─ Business logic preserved

3️⃣ PLUGIN ISOLATION
   ├─ Test in separate workspace
   ├─ Validate before integration
   └─ Rollback safety guaranteed

4️⃣ DEPENDENCY WAVES
   ├─ 4 migration waves defined
   ├─ 8-10 week timeline
   └─ Parallel work opportunities

RESULT: 17-plugin CMS → Manageable 4-wave migration
```

---

## 💡 Key Takeaways (1 minute)

**SAY THESE POINTS CLEARLY:**

1. **"Natural boundaries prevent cascading failures"**
   - Controllers = Complete features
   - Plugins = Deployable units
   - Test boundaries independently

2. **"Dependencies determine order"**
   - Never migrate dependents before dependencies
   - Use wave structure to show progress
   - Critical path reveals minimum timeline

3. **"Isolation enables safe testing"**
   - Isolated workspace = No production risk
   - All tests pass before integration
   - Rollback is cheap in isolation

4. **"Claude Code does the heavy lifting"**
   - Dependency analysis automated
   - Migration checklists generated
   - Time estimates data-driven

---

## ⚠️ Component Migration Anti-Patterns (30 seconds)

**QUICK MENTION:**
```
❌ DON'T:
   • Migrate all controllers at once
   • Ignore plugin dependencies
   • Skip isolation testing
   • Start with complex plugins

✅ DO:
   • Start with simple, low-dependency plugins (locale)
   • Test each unit in isolation
   • Follow dependency waves
   • Use Claude to generate checklists
```

---

## 🎬 Transition to Next Slide (30 seconds)

**SAY:**
> "We've structured our migration into safe, testable units using natural boundaries. Now let's talk about the safety nets that catch issues before they reach production - automated testing and validation workflows..."

**[ADVANCE TO NEXT SLIDE]**

---

## 🚨 Emergency Fallback Plan

If technical issues occur during demo:

### Fallback Option 1: Pre-recorded Screenshots
Have screenshots ready of:
- Claude's complexity analysis response
- Controller migration checklist
- Dependency wave diagram

### Fallback Option 2: Static Dependency Graph
Show the Mermaid diagram from `PLUGINS_DEPENDENCY_MAP.md` as static image

### Fallback Option 3: Whiteboard Explanation
Draw simple diagram:
```
Plugin A (simple) ──► Migrate Week 1
                       ↓
Plugin B (depends) ──► Migrate Week 2
                       ↓
Plugin C (depends) ──► Migrate Week 3
```

Focus on CONCEPTS rather than live demo if tech fails.

---

## 📈 Success Metrics to Highlight

**MENTION DURING Q&A:**

```
Before Component Strategy:
├─ No clear starting point
├─ Fear of breaking dependencies
├─ Unknown timeline
└─ High risk

After Component Strategy:
├─ Clear 4-wave plan
├─ Isolated testing per component
├─ 8-10 week timeline
└─ Low risk (can rollback each wave)
```

---

## 🔗 Related Documentation

**Files Used in Demo:**
- `migration-docs/CONTROLLERS_DEPENDENCY_MAP.md` - Controller hierarchy
- `migration-docs/PLUGINS_DEPENDENCY_MAP.md` - Plugin dependencies
- `migration-docs/DEPENDENCY_MATRIX.md` - Detailed dependency matrix
- `migration-docs/test-plans/TEST_PLAN_LOCALE.md` - Locale plugin test plan

**Code Files Examined:**
- `quickapps-cakephp5/vendorCake3/quickapps-plugins/locale/src/Controller/Admin/ManageController.php` - Example controller
- `quickapps-cakephp5/vendorCake3/quickapps-plugins/locale/src/Controller/AppController.php` - Plugin base

---

## 🎯 Presenter Tips

1. **Timing Control**
   - Step 1 (Analysis): 2.5 min ⏱️
   - Step 2 (Controller): 3 min ⏱️
   - Step 3 (Isolation): 2.5 min ⏱️
   - Step 4 (Waves): 1.5 min ⏱️
   - Summary: 1 min ⏱️
   - Total: 10.5 min (leaves 1.5 min buffer)

2. **Engagement Points**
   - Pause after Claude generates complexity analysis
   - Ask audience: "What would you have started with?" (expect wrong answers)
   - Show surprise when Claude recommends locale (the right choice)

3. **Common Audience Questions**
   - Q: "Can we migrate multiple waves simultaneously?"
     A: "Only plugins within same wave - never cross waves"

   - Q: "What if we find a bug after integration?"
     A: "That's why we have isolation tests - should catch 95% before integration"

   - Q: "How do we handle circular dependencies?"
     A: "Identify and break them first - or migrate as single unit"

---

## ✅ Pre-Presentation Checklist

**30 minutes before:**
- [ ] Git branch `item4` checked out
- [ ] Terminal open in W3 directory
- [ ] Claude Code authenticated and ready
- [ ] Documentation files confirmed present
- [ ] Controller count verified (43)
- [ ] Plugin count verified (17)

**5 minutes before:**
- [ ] Run all pre-demo setup commands
- [ ] Have prompts ready to copy-paste
- [ ] Browser tabs closed (minimize distractions)
- [ ] Screen sharing tested
- [ ] Backup screenshots ready

**During presentation:**
- [ ] Speak clearly and not too fast
- [ ] Pause for Claude responses
- [ ] Highlight key insights from Claude
- [ ] Stay within time limits
- [ ] Show enthusiasm for the automation!

---

## 📝 Post-Demo Actions

After successful demo:
1. Share this guide with attendees
2. Provide GitHub link to the W3 repository
3. Offer 1-on-1 Claude Code migration consultations
4. Collect feedback on what prompts worked best

---

**Last Updated**: September 30, 2025
**Presenter**: Carlos Dukiz
**Branch**: item4
**Estimated Demo Duration**: 10.5 minutes (with 1.5min buffer)
