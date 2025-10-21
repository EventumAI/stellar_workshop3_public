# Workbook 05: Data & Deployment Safety

## Introduction

Welcome to the most critical workbook in this series. **Data loss is unrecoverable. Deployment failures are embarrassing.** This workbook demonstrates the belt-and-suspenders approach to production safety: the dual-write database pattern combined with feature flags.

You'll learn how to migrate authentication systems with **zero downtime** and **instant rollback** capability. This is not about speed—it's about safety. Database migrations measured in weeks, not hours.

> **Note**: If you haven't set up the environments yet, complete [00-setup.md](./00-setup.md) first.

---

## Learning Objectives

By the end of this workbook, you will be able to:

- **Assess migration risks** for critical database tables like user authentication
- **Implement dual-write patterns** that keep both old and new systems working simultaneously
- **Use feature flags** for instant rollback without redeployment
- **Create emergency rollback procedures** that execute in under 30 seconds
- **Understand safety timelines** for database migrations (add → dual-write → switch → cleanup)

---

## Key Concepts

### 🛡️ Dual-Write Pattern
Write to BOTH old and new database columns/tables during migration. This ensures:
- Old system continues working (CakePHP 3 reads `api_token`)
- New system gets data (CakePHP 5 reads `auth_token`)
- Zero data loss if rollback needed
- Instant switchover capability

### 🚦 Feature Flags
Runtime switches that enable/disable functionality without deployment:
- **Instant rollback**: Flip flag from `1` to `0` in database
- **Gradual rollout**: Enable for 5% → 25% → 50% → 100% of users
- **A/B testing**: Compare old vs new system performance
- **Non-technical control**: Product managers can control rollout

### ⏱️ Safety Timeline
Database migrations follow a strict timeline:
```
Week 1: Add Column     Week 2: Dual-Write    Week 3: Switch Reads    Week 6: Remove Old
   ↓                       ↓                      ↓                       ↓
[Zero Risk]         [Both Systems Work]    [New System Active]     [Clean Cleanup]
```

**Never rush this timeline.** The dual-write period provides safety.

---

## Target Task

**Migrate user authentication** from CakePHP 3's `api_token` column to CakePHP 5's new `auth_token` column with zero downtime.

**Source**: `quickapps-db` (MySQL 5.7) - CakePHP 3 `users` table
**Target**: `quickapps5-db` (MySQL 8.0) - CakePHP 5 `users` table

**Critical Constraints**:
- Cannot break existing user logins
- Must support instant rollback
- Zero data loss tolerance
- Production users actively authenticating

---

## Required Resources

Your AI assistant will reference these documents:

- **migration-docs/DATABASE_SCHEMA.md** - Complete schema documentation for both databases
- **migration-docs/DATABASE_MIGRATION_ISSUES.md** - Known MySQL 5.7 → 8.0 migration issues
- **migration-docs/unknown_patterns/02_snapshot_configuration.md** - Settings/configuration system

---

## Prerequisites

✅ Both Docker environments running (see [00-setup.md](./00-setup.md))
✅ Access to both databases:
```bash
docker exec -it quickapps-db mysql -u quickapps -pquickapps123 quickapps
docker exec -it quickapps5-db mysql -u quickapps5 -pquickapps123 quickapps5
```

---

## Part 1: Risk Analysis & Feature Flag Foundation

**⏱️ Time: ~5 minutes**

### Objective

Before touching any database, understand what you're protecting against. Learn to assess migration risks and set up the feature flag infrastructure that will protect you throughout the migration.

### Key Principle

> 🎯 **Understand the blast radius.** Know exactly what breaks if this migration fails, how many users are affected, and what the rollback procedure must accomplish.

### Your Task

Analyze the risk of migrating user authentication and set up the feature flag system that will enable instant rollback.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
We need to migrate the users table authentication from cakephp3 project to CakePHP5 project.

PART 1 - Risk Assessment:
Analyze the authentication migration risk:

1. Current schema risk: What breaks if we change auth fields?
   - Check the users table structure in both databases
   - Identify authentication-related columns
   - Map dependencies (sessions, tokens, API keys)

2. Data volume: How many user records could be affected?
   - Check current user count in database
   - Identify active vs inactive accounts

3. Downtime impact: What happens if auth fails during migration?
   - User login failures
   - Active session invalidation
   - API authentication breakage

PART 2 - Feature Flag Foundation:
Create a feature flag system for this migration:

1. Design the feature_flags table schema:
   - name (primary key)
   - enabled (boolean)
   - rollout_percentage (0-100)
   - updated (timestamp)

2. Show the SQL to create this table in quickapps-db

3. Show example feature flag usage:
   ```php
   if (FeatureFlag::isEnabled('useCake5Auth')) {
       // New CakePHP 5 authentication
   } else {
       // Keep CakePHP 3 auth working
   }
   ```

DO NOT implement yet - just show the analysis and design.

Reference migration-docs/DATABASE_MIGRATION_ISSUES.md for auth table risks.
Reference migration-docs/unknown_patterns/02_snapshot_configuration.md for settings system integration.
```

### 🤔 What to Expect

The AI should provide:

1. **Risk Analysis Report**:
   - Specific columns involved (`api_token`, session-related fields)
   - Estimated number of affected users
   - Clear description of failure scenarios

2. **Feature Flags Table Schema**:
   ```sql
   CREATE TABLE IF NOT EXISTS feature_flags (
       name VARCHAR(100) PRIMARY KEY,
       enabled BOOLEAN DEFAULT FALSE,
       rollout_percentage INT DEFAULT 0,
       updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

3. **Usage Examples**: PHP code showing how to check flags

### ✅ Success Criteria

- [ ] AI identified authentication-related columns in users table
- [ ] Risk assessment covers data loss, downtime, and user impact
- [ ] Feature flag schema includes rollout_percentage for gradual deployment
- [ ] Example code shows both old and new auth paths
- [ ] NO implementation yet—analysis only

### 🚨 Red Flags

- ❌ AI suggests "just update the column name" without dual-write
- ❌ Risk analysis says "low risk" for authentication changes (it's ALWAYS high risk!)
- ❌ Feature flag design lacks rollout_percentage (gradual rollout is critical)
- ❌ AI proposes implementing changes before you've reviewed the plan

### 📊 What You Learned

- Authentication migrations are **high risk** even for "simple" column changes
- Feature flags must support **gradual rollout**, not just on/off switches
- Risk assessment identifies the **blast radius** before making changes
- Always analyze **downtime impact** for user-facing features

---

## Part 2: Dual-Write Database Pattern

**⏱️ Time: ~6 minutes**

### Objective

Implement the dual-write pattern that writes authentication data to BOTH old and new columns. This is the safety net that allows instant rollback and keeps both systems working during migration.

### Key Principle

> 🛡️ **Never drop old columns immediately.** Dual-write keeps both CakePHP 3 and CakePHP 5 authentication working simultaneously. The old column stays until you're confident the new system works.

### Your Task

Add a new auth column and create dual-write code that updates both old and new columns simultaneously.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Implement safe column migration for users.auth_token using dual-write pattern.

STEP 1 - Add new column (don't remove old):
Show the SQL commands to:

1. Add auth_token column to users table
   ```sql
   ALTER TABLE users ADD COLUMN auth_token VARCHAR(255) AFTER api_token;
   ```

2. Backfill existing data from api_token to auth_token
   ```sql
   UPDATE users SET auth_token = api_token WHERE api_token IS NOT NULL;
   ```

3. Verify both columns exist and contain same data

STEP 2 - Dual-write application code:
Create model code that writes to BOTH columns:

```php
// In User model - write to BOTH columns
public function setAuthToken($token) {
    $this->api_token = $token;      // CakePHP 3 still reads this
    $this->auth_token = $token;     // CakePHP 5 will read this
    return $this;
}

// Feature flag wrapper for authentication
public function authenticate($credentials) {
    if (FeatureFlag::isEnabled('useCake5Auth')) {
        return $this->authenticateCake5($credentials);
    }
    return $this->authenticateCake3($credentials);
}
```

STEP 3 - Verification:
Show how to verify both columns stay in sync:
- Query to check for mismatches
- Strategy to handle write conflicts

This allows instant switching between auth systems!

DO NOT execute SQL yet - show me the complete plan first.
```

### 🤔 What to Expect

The AI should provide:

1. **SQL Commands**:
   - `ALTER TABLE` to add auth_token column
   - `UPDATE` statement to backfill existing data
   - `SELECT` query to verify data consistency

2. **Dual-Write Code**:
   - Method that writes to BOTH `api_token` and `auth_token`
   - Feature flag check that switches between old/new authentication
   - Clear comments explaining which system reads which column

3. **Verification Strategy**:
   ```sql
   SELECT COUNT(*) FROM users
   WHERE api_token != auth_token OR (api_token IS NULL) != (auth_token IS NULL);
   ```
   (Should return 0 if columns are in sync)

### ✅ Success Criteria

- [ ] New column added WITHOUT removing old column
- [ ] Backfill SQL copies existing data safely
- [ ] Dual-write code updates BOTH columns on every auth change
- [ ] Feature flag controls which column is READ from
- [ ] Verification query checks for synchronization issues
- [ ] Plan shown but NOT executed yet

### 🚨 Red Flags

- ❌ AI suggests dropping api_token column (NEVER drop immediately!)
- ❌ Code only writes to one column (defeats dual-write purpose)
- ❌ No backfill step (new column would be empty!)
- ❌ Missing verification—you must confirm columns stay in sync
- ❌ AI executes SQL without your explicit approval

### 📊 What You Learned

- **Dual-write = both systems work**: Old code reads `api_token`, new code reads `auth_token`
- **Backfilling is critical**: New column must contain existing data before switchover
- **Verification prevents silent failures**: Columns can drift out of sync
- **Old column stays for weeks**: Safety requires patience—don't rush cleanup

---

## Part 3: Feature Flag Protection Layer

**⏱️ Time: ~5 minutes**

### Objective

Wrap the migration in comprehensive feature flags that enable instant rollback and gradual rollout. This layer lets you switch between authentication systems without deploying code.

### Key Principle

> 🚦 **Runtime control beats code deployment.** When authentication breaks at 2 AM, you need a database flag flip (30 seconds), not a code deploy (30 minutes).

### Your Task

Implement feature flag protection that controls which authentication system is active, with gradual rollout capability.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Wrap our authentication migration with comprehensive feature flags for instant rollback.

STEP 1 - Application Controller Integration:
Show how to integrate feature flags in AppController:

```php
// In AppController
public function initialize(): void {
    if (FeatureFlag::isEnabled('useCake5Auth')) {
        // New CakePHP 5 authentication
        $this->loadComponent('Authentication.Authentication');
        $this->loadComponent('Authorization.Authorization');
    } else {
        // Keep CakePHP 3 auth working
        $this->loadComponent('Auth', [
            'authenticate' => ['Form'],
            'loginAction' => ['controller' => 'Users', 'action' => 'login']
        ]);
    }
}
```

STEP 2 - Gradual Rollout Capability:
Implement percentage-based rollout:

```php
// Gradual rollout - enable for subset of users
public function isNewAuthEnabled($user = null): bool {
    $flag = FeatureFlag::get('useCake5Auth');

    if ($user && isset($flag['rollout_percentage'])) {
        // Consistent hashing - same user always gets same result
        $hash = crc32('auth-' . $user->id);
        return ($hash % 100) < $flag['rollout_percentage'];
    }

    return $flag['enabled'] ?? false;
}
```

STEP 3 - Admin Interface (Simple):
Create a basic admin interface for non-technical staff to control rollout:
- View current flag status
- Enable/disable flag
- Adjust rollout percentage
- Show affected user count

This allows product managers to control migration without developer intervention.

Show me the implementation plan.
```

### 🤔 What to Expect

The AI should provide:

1. **AppController Integration**:
   - Feature flag check in `initialize()` method
   - Conditional loading of old (Auth) vs new (Authentication) components
   - Clear separation between CakePHP 3 and 5 authentication

2. **Gradual Rollout Logic**:
   - Consistent hashing (same user always gets same result)
   - Percentage-based rollout (5% → 25% → 100%)
   - Fallback to global enabled/disabled setting

3. **Admin Interface Design**:
   - Simple form to toggle flags
   - Slider for rollout percentage
   - Display of affected user counts

### ✅ Success Criteria

- [ ] Feature flag check happens BEFORE loading authentication components
- [ ] Both old (Auth) and new (Authentication) systems can be loaded
- [ ] Gradual rollout uses consistent hashing (user always gets same experience)
- [ ] Admin interface allows non-technical control
- [ ] Code clearly documents which system is active in each branch

### 🚨 Red Flags

- ❌ Loading both authentication systems simultaneously (creates conflicts!)
- ❌ Random rollout instead of consistent hashing (user experience fluctuates)
- ❌ No admin interface—only developers can change flags
- ❌ Feature flag check happens AFTER authentication is loaded (too late!)

### 📊 What You Learned

- **Feature flags control runtime behavior**: No deployment needed to rollback
- **Consistent hashing prevents flipping**: User doesn't switch between systems randomly
- **Gradual rollout reduces risk**: Test on 5% before enabling for everyone
- **Non-technical control**: Product owners can manage rollout without developers

---

## Part 4: Emergency Rollback System

**⏱️ Time: ~4 minutes**

### Objective

When authentication breaks in production, you need to rollback in under 30 seconds. Learn to create an emergency rollback script that instantly restores the old authentication system.

### Key Principle

> 🚨 **Test rollback BEFORE you need it.** The emergency procedure must be practiced, documented, and executable by anyone on-call—even at 3 AM.

### Your Task

Create an emergency rollback script that disables new authentication and verifies old authentication works.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Create instant rollback capability for authentication migration failure.

Create an emergency rollback script:

```bash
#!/bin/bash
# emergency-rollback.sh - Execute when auth breaks

echo "🚨 EMERGENCY AUTH ROLLBACK"

# Step 1: Disable new auth immediately (30 seconds max)
docker exec quickapps-db mysql -u quickapps -pquickapps123 quickapps -e "
    UPDATE feature_flags
    SET enabled = 0, rollout_percentage = 0
    WHERE name = 'useCake5Auth';
"

# Step 2: Clear auth caches (prevent stale auth state)
# Show appropriate cache clearing command for the project

# Step 3: Verify old auth works
echo "🔍 Testing old authentication..."
curl -X POST http://localhost:8080/api/login \
    -d '{"username":"admin","password":"admin"}' \
    -H "Content-Type: application/json"

# Expected: 200 OK with session token

echo "✅ Rollback complete - old auth restored"

# Step 4: Alert team
echo "📢 Alert: Post to team Slack channel about rollback"
# Show example Slack webhook notification
```

Additional requirements:
1. Document WHEN to use this script (failure symptoms)
2. Document WHO can execute it (on-call checklist)
3. Document WHAT to check after rollback (verification steps)
4. Estimate total rollback time (should be < 30 seconds)

Rollback must complete with zero data loss!
```

### 🤔 What to Expect

The AI should provide:

1. **Emergency Rollback Script**:
   - SQL command to disable feature flag immediately
   - Cache clearing commands
   - Authentication verification test (curl command)
   - Team notification mechanism

2. **Rollback Documentation**:
   - **When to rollback**: Symptoms like increased 401 errors, failed logins
   - **Who can execute**: On-call engineers, SREs, anyone with database access
   - **What to verify**: Old authentication works, no stuck users

3. **Timing Estimate**: Total rollback time < 30 seconds

### ✅ Success Criteria

- [ ] Single SQL command disables feature flag instantly
- [ ] Script clears authentication caches to prevent stale state
- [ ] Verification test confirms old authentication works
- [ ] Documentation covers WHEN, WHO, WHAT
- [ ] Rollback completes in under 30 seconds
- [ ] Zero data loss—both columns still contain valid data

### 🚨 Red Flags

- ❌ Rollback requires code deployment (too slow!)
- ❌ Script drops the auth_token column (irreversible!)
- ❌ No verification step—doesn't confirm old auth works
- ❌ No team notification—failures go unnoticed
- ❌ Rollback takes minutes instead of seconds

### 📊 What You Learned

- **Emergency rollback is a single SQL UPDATE**: No deployment needed
- **Verification is mandatory**: Confirm old system works before declaring success
- **Documentation saves lives**: At 3 AM, clear instructions matter
- **Practice rollback regularly**: Don't discover broken rollback during emergency

---

## Part 5: Safety Verification & Timeline

**⏱️ Time: ~5 minutes**

### Objective

Understand the complete safety timeline for database migrations and verify that all safety mechanisms are in place before proceeding.

### Key Principle

> ⏱️ **Database changes take weeks, not hours.** The dual-write period provides the safety buffer. Rushing cleanup breaks rollback capability.

### Your Task

Review the complete migration timeline and verify all safety mechanisms are ready.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Verify our authentication migration safety mechanisms and create timeline.

PART 1 - Safety Checklist:
Verify these mechanisms are in place:

1. ✅ Dual-write code updates both api_token and auth_token
2. ✅ Feature flag controls which column is READ from
3. ✅ Emergency rollback script tested and documented
4. ✅ Verification queries check column synchronization
5. ✅ Admin interface allows non-technical rollout control
6. ✅ Gradual rollout enabled (start at 5%, not 100%)

PART 2 - Migration Timeline:
Create a week-by-week timeline:

```
Week 1: Add Column + Backfill
  - Add auth_token column
  - Backfill from api_token
  - Deploy dual-write code
  - Status: Both columns synchronized, old auth active
  - Rollback: Instant (just don't enable flag)

Week 2-3: Gradual Rollout
  - Enable flag for 5% of users
  - Monitor error rates, login success
  - Increase to 25%, then 50%, then 100%
  - Status: Both auth systems work, new system being tested
  - Rollback: Instant (flip flag to 0)

Week 4-5: Monitoring Period
  - 100% of users on new auth
  - Monitor for edge cases
  - Verify old column still updated (dual-write active)
  - Status: New auth fully active, old auth still maintained
  - Rollback: Instant (flip flag to 0)

Week 6+: Cleanup Phase
  - Remove dual-write code (write only to auth_token)
  - Wait 1 more week
  - Drop api_token column
  - Status: Migration complete, rollback no longer possible
```

PART 3 - Risk Analysis:
For each week, identify:
- What can go wrong
- How to detect problems
- How to rollback
- Risk level (Low/Medium/High)

Show me the complete timeline and risk analysis.
```

### 🤔 What to Expect

The AI should provide:

1. **Safety Checklist**: Verification that all protection mechanisms are in place

2. **Week-by-Week Timeline**:
   - Week 1: Add column, backfill, deploy dual-write
   - Weeks 2-3: Gradual rollout (5% → 100%)
   - Weeks 4-5: Monitoring with dual-write still active
   - Week 6+: Remove dual-write, then drop old column

3. **Risk Analysis Per Week**:
   - Week 1: Low risk (nothing enabled yet)
   - Weeks 2-3: Medium risk (new auth being tested)
   - Weeks 4-5: Low risk (dual-write provides safety)
   - Week 6+: High risk (rollback no longer possible after column drop)

### ✅ Success Criteria

- [ ] Timeline shows 6+ weeks from start to column drop
- [ ] Gradual rollout starts at 5%, not 100%
- [ ] Dual-write remains active for 4-5 weeks minimum
- [ ] Old column (api_token) not dropped until Week 6+
- [ ] Each week has clear rollback capability documented
- [ ] Risk analysis identifies when rollback becomes impossible

### 🚨 Red Flags

- ❌ Timeline shows "drop column in Week 2" (way too fast!)
- ❌ Rollout goes directly to 100% without gradual steps
- ❌ Dual-write code removed before monitoring period complete
- ❌ No risk analysis for cleanup phase (most dangerous period!)

### 📊 What You Learned

- **Database migrations measured in weeks**: Safety requires patience
- **Dual-write period is the safety buffer**: Don't rush to cleanup
- **Gradual rollout reduces risk**: Test on subset before full deployment
- **Column drops are one-way**: Once old column is gone, rollback is impossible

---

## Congratulations! 🎉

You've learned the belt-and-suspenders approach to production safety: **dual-write patterns** combined with **feature flags**. This is the gold standard for zero-downtime database migrations.

### What You've Accomplished

✅ **Risk assessment** for authentication migration
✅ **Dual-write pattern** that keeps both systems working
✅ **Feature flag protection** for instant rollback
✅ **Emergency procedures** that execute in < 30 seconds
✅ **Safety timeline** that measures migrations in weeks

### Real-World Impact

In production scenarios, these techniques have:
- Prevented authentication outages affecting millions of users
- Enabled instant rollback when new systems showed unexpected bugs
- Allowed gradual testing (5% of users) before full deployment
- Provided data safety during complex database schema changes

---

## Key Takeaways

### ✅ What Makes Safe Migrations

1. **Dual-write = zero data loss**
   - Write to both old and new columns/tables
   - Both systems work during migration period
   - Instant switchover capability

2. **Feature flags = instant rollback**
   - Single SQL UPDATE disables new system
   - No code deployment needed
   - Rollback completes in < 30 seconds

3. **Timeline = safety buffer**
   - Week 1: Add column, backfill, deploy dual-write
   - Weeks 2-3: Gradual rollout (5% → 100%)
   - Weeks 4-5: Monitoring period
   - Week 6+: Cleanup (remove old column)

4. **Test rollback BEFORE emergency**
   - Practice emergency procedures
   - Document clearly (3 AM-friendly instructions)
   - Verify old system works after rollback

### ❌ What Breaks Migrations

1. ❌ **Dropping columns same day as adding** → Breaks instant rollback
2. ❌ **Deploying without rollback plan** → Failure becomes unrecoverable
3. ❌ **Skipping dual-write phase** → Forces downtime during switchover
4. ❌ **Rushing database changes** → No time to discover edge cases

### 🎯 The Golden Rules

> **"Dual-write keeps both systems working"** - Never force users to one system immediately

> **"Feature flags enable instant rollback"** - Runtime control beats code deployment

> **"Database changes take weeks, not hours"** - Patience prevents disasters

> **"Test rollback BEFORE you need it"** - Emergency procedures must be practiced

---

## Reflection Questions

1. **Why is dual-write safer than a direct column rename?**
   - Consider: What happens if you need to rollback?
   - Consider: What if the new auth system has a bug?

2. **Why does gradual rollout start at 5% instead of 100%?**
   - Consider: How many users are affected if something breaks?
   - Consider: How quickly can you detect problems?

3. **Why wait 4-5 weeks before dropping the old column?**
   - Consider: What edge cases might only appear after days?
   - Consider: What happens if you discover a bug in Week 3?

4. **What would happen if you removed dual-write code in Week 2?**
   - Consider: Can you still rollback if needed?
   - Consider: What if columns drift out of sync?

5. **Why must rollback take < 30 seconds?**
   - Consider: How many failed login attempts happen per minute?
   - Consider: What's the business impact of authentication downtime?

---

## Additional Resources

- **migration-docs/DATABASE_SCHEMA.md** - Complete database schema for both systems
- **migration-docs/DATABASE_MIGRATION_ISSUES.md** - Known MySQL 5.7 → 8.0 issues
- **migration-docs/unknown_patterns/02_snapshot_configuration.md** - Settings/configuration system

---

> **Important**: This workbook demonstrates the PROCESS of safe database migration with dual-write and feature flags. In a real production scenario, you would:
> - Have comprehensive monitoring and alerting for authentication failures
> - Run load tests to ensure new system handles production traffic
> - Have documented rollback procedures accessible to all on-call engineers
> - Practice emergency rollback regularly (quarterly "game days")
> - Coordinate with security team for authentication system changes
> - Have full database backups before any schema changes

**Remember**: Data loss is unrecoverable. Take your time. Measure migrations in weeks. Both systems working beats one broken system.
