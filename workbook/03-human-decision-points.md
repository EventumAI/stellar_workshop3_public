# Workbook 03: Human Decision Points

## Introduction

Welcome to the third hands-on workbook on safe AI-assisted migration!

In this workbook, you'll learn **where human judgment is irreplaceable** and how to establish clear decision authority between you and AI. AI is powerful at analyzing code patterns, but humans are essential for business context, risk assessment, and final decisions.

### Why Does This Matter?

AI can suggest technically perfect solutions that are business disasters:
- Migrating critical code right before Black Friday
- Breaking compliance requirements the AI doesn't know about
- Ignoring performance SLAs for your biggest customer
- Changing API contracts that third-party integrations depend on

**This workbook teaches you to be the navigator while AI drives.**

## Learning Objectives

By the end of this workbook, you will be able to:

- **Provide business context** that AI cannot know (revenue, compliance, timing)
- **Identify decision points** where human judgment is required
- **Pair program effectively** with AI using Navigator/Driver roles
- **Validate business logic** with real-world edge cases
- **Override AI suggestions** when business context demands it
- **Document decisions** with the reasoning for future reference
- **Understand collaboration boundaries** between human and AI authority

## Key Concepts

### 🧠 Human Context First

**The Principle:**
AI analyzes code structure and patterns. Humans provide business impact, compliance requirements, customer needs, and organizational constraints.

**Example:**
- **AI sees**: "This method processes user data"
- **Human knows**: "This handles $2M/month for 50,000 GDPR-protected EU customers, and Black Friday is in 2 weeks"

### 🎯 AI Suggests, Human Decides

**The Principle:**
AI can recommend technical approaches with high confidence, but humans have final authority on all decisions, especially business-critical ones.

**Example:**
- **AI recommends**: "Migrate this module now, 95% confidence"
- **Human decides**: "Defer - business timing inappropriate, too close to critical sales period"

### 🔍 Domain Knowledge Prevents Disasters

**The Principle:**
You know your business, your customers, your compliance requirements, and your organizational constraints. AI doesn't. This knowledge prevents technically correct but business-disastrous decisions.

**Example:**
- **AI suggests**: "Optimize this caching layer"
- **Human catches**: "That breaks Salesforce integration our biggest client depends on"

## Target Task

**EAV Plugin File**: `quickapps-cakephp5/src/plugins/eav/src/Model/Behavior/EavBehavior.php`

**Context**: This is the same EAV behavior from Workbook 02, but now we'll approach it with business context and human decision authority.

**Business Reality**:
- Handles $2M/month in product configurations
- 50,000 existing records that cannot be lost
- EU customers require GDPR compliance
- Biggest client depends on <100ms performance
- Black Friday is 2 weeks away

**Goal**: Learn where human judgment overrides AI suggestions

## Required Resources

Before starting, ensure you have access to:

- `migration-docs/PROJECT_AUDIT.md` - Business context and architecture
- `migration-docs/unknown_patterns/03_eav_implementation.md` - EAV complexity details
- `migration-docs/API.md` - Business logic and API contracts to preserve
- `migration-docs/CakePHP_3_TO_5_GUIDE.md` - Technical migration patterns

## Prerequisites

### Environment Setup

> **Note**: If you haven't set up the environments yet, complete [00-setup.md](./00-setup.md) first.

Make sure the CakePHP 5 environment is running:
- CakePHP 5: http://localhost:8090

### Previous Workbooks

This workbook builds on concepts from:
- [01-migration-fundamentals.md](./01-migration-fundamentals.md) - Smallest Change + Review First
- [02-quality-testing-loop.md](./02-quality-testing-loop.md) - Red-Green-Refactor cycle

### Open the Target File

```bash
# View the EAV behavior file we'll be working with
cat quickapps-cakephp5/src/plugins/eav/src/Model/Behavior/EavBehavior.php

# Or open in your editor
quickapps-cakephp5/src/plugins/eav/src/Model/Behavior/EavBehavior.php
```

---

## ⏱️ Estimated Time: 32-37 minutes

In the following sections, we'll walk through human-AI collaboration patterns where your business knowledge and decision authority are critical.

---

## Part 1: Human Context & Review Gates

**⏱️ Time: ~7 minutes**

### Objective

Learn to provide business context that transforms AI's technical analysis into business-aware risk assessment. AI cannot know your revenue, compliance requirements, customer impact, or timing constraints - you must provide this critical information.

### Key Principle

> 🧠 **Human Context First.** AI analyzes code; humans provide business impact. Without context, AI makes technically sound but potentially business-disastrous recommendations.

### Why This Matters

Consider two scenarios:

**Without context:**
- AI: "This migration looks straightforward, 95% confidence, let's proceed"

**With context:**
- Human: "This handles $2M/month for 50k users, Black Friday is in 2 weeks"
- AI: "Given the business context, I recommend deferring until after peak season, with staged rollout and extensive monitoring"

The technical analysis is the same. The business decision is completely different.

### Your Task

Provide comprehensive business context to AI before any EAV migration work, and establish review checkpoints.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Claude, I need to migrate the EAV behavior but there's business context you need:

HUMAN CONTEXT:
1. This EAV system handles $2M/month in product configurations
2. We have 50,000 existing records that CANNOT be lost
3. EU customers require GDPR compliance for attribute data
4. Our biggest client depends on custom field performance (<100ms)
5. Black Friday is in 2 weeks - zero tolerance for issues

REVIEW CHECKPOINT QUESTIONS:
1. What are the main risks with your suggested approach?
2. How do we maintain GDPR compliance during migration?
3. What's your rollback strategy if EAV data gets corrupted?
4. How do we test with 50k records without affecting production?

Before any implementation, create a risk assessment that accounts for:
- Business impact (revenue at risk)
- Customer impact (user experience)
- Compliance requirements (legal obligations)
- Performance constraints (SLA requirements)

Reference migration-docs/unknown_patterns/03_eav_implementation.md for complexity.
```

### 🤔 What to Expect

The AI should provide:

#### 1. Revised Risk Assessment

**Before context (typical AI response):**
```
Risk Level: Low-Medium
- Technical complexity: Manageable
- Breaking changes: Minimal
- Confidence: 85%
```

**After context (business-aware response):**
```
Risk Level: HIGH
- Revenue at risk: $2M/month
- User impact: 50,000 users
- Timing: CRITICAL (Black Friday in 2 weeks)
- Compliance: GDPR Article 17 requirements
- Recommendation: DEFER until after peak season
```

#### 2. GDPR Compliance Strategy

The AI should acknowledge:
- Right to be forgotten (Article 17)
- Data export requirements
- Geographic data tagging for EU customers
- Audit trail requirements

#### 3. Rollback Strategy

Something like:
```
1. Full database backup before migration
2. Shadow mode testing (new code runs but doesn't save)
3. Feature flag to switch back instantly
4. Data validation checksums
5. Automated rollback if corruption detected
```

#### 4. Production-Safe Testing Plan

```
1. Clone 50k records to staging environment
2. Load testing with production-like traffic
3. Performance benchmarks (<100ms SLA)
4. Gradual rollout: 1% → 5% → 25% → 100%
5. Monitoring dashboards for each phase
```

### ✅ Success Criteria

You've completed this step when:
- [ ] AI has acknowledged ALL five business context points
- [ ] Risk assessment changed from technical-only to business-aware
- [ ] AI recommended timing considerations (Black Friday constraint)
- [ ] GDPR compliance strategy is detailed
- [ ] Rollback strategy is comprehensive
- [ ] Testing plan accounts for 50k production records
- [ ] AI's confidence reflects business complexity, not just technical complexity

### 🚨 Red Flags

Stop immediately if the AI:
- ❌ Dismisses business context as "nice to have"
- ❌ Maintains high confidence despite critical timing (Black Friday)
- ❌ Suggests "we'll handle compliance later"
- ❌ Doesn't provide rollback strategy
- ❌ Recommends testing in production
- ❌ Ignores the $2M revenue impact

### 💡 Pro Tip: Types of Context to Always Provide

**Financial Context:**
- Revenue at risk
- Cost of downtime
- Budget constraints

**User Context:**
- Number of affected users
- User tolerance for issues
- Support team capacity

**Compliance Context:**
- GDPR, HIPAA, PCI-DSS, SOX requirements
- Industry regulations
- Company policies

**Timing Context:**
- Critical business periods (Black Friday, tax season, etc.)
- Maintenance windows
- Team availability

**Technical Context:**
- Performance SLAs
- Integration dependencies
- Infrastructure constraints

### 📊 What You Learned

- How business context transforms AI's risk assessment
- Why AI confidence scores don't include business factors
- How to establish review checkpoints before implementation
- The critical questions to ask before any migration
- Why timing and compliance are human decisions, not technical ones

> **Important**: This workbook demonstrates the PROCESS of providing business context. In your real environment, you'll have different constraints, compliance requirements, and timing considerations. Always provide YOUR specific business context.

---

## Part 2: Pair Programming Session

**⏱️ Time: ~8 minutes**

### Objective

Learn to pair program with AI using Navigator/Driver roles, where you provide direction and can stop implementation when business knowledge reveals issues the AI couldn't anticipate.

### Key Principle

> 🎯 **Human navigates, AI drives - but human can brake.** You set direction, AI implements, but you have authority to stop and redirect based on business knowledge.

### Why This Matters

In traditional pair programming:
- **Navigator**: Thinks strategically, provides direction, catches problems
- **Driver**: Focuses on implementation, writes code

With AI:
- **Human (Navigator)**: Knows business logic, integrations, edge cases, constraints
- **AI (Driver)**: Implements patterns, writes code, suggests technical approaches
- **Critical**: Human can STOP AI when business knowledge reveals hidden issues

### Your Task

Conduct a pair programming session where you navigate and AI drives, demonstrating your ability to stop and redirect based on business knowledge.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Let's pair program this EAV migration:

HUMAN (Navigator): "Start with the getAttribute method - it's called 10k times/hour"
AI (Driver): [Shows implementation approach]

HUMAN INTERVENTION: "Stop - that approach breaks our caching layer"

Our custom caching stores serialized data in Redis with 5-minute TTL.
Your approach changes the data structure, breaking cache compatibility.

BETTER APPROACH:
1. Maintain cache format during transition
2. Add compatibility layer for old cache entries
3. Gradual cache invalidation strategy

Show me revised implementation that preserves cache compatibility.

AI: [Shows revised approach]

HUMAN REVIEW: "Good, but add these business rules you missed:
- Price fields must round to 2 decimals (financial regulation)
- Product attributes need audit trail (SOX compliance)
- EU customer data needs geographic tagging (GDPR Article 17)"

Implement with these constraints.
```

### 🤔 What to Expect

The AI should demonstrate this flow:

#### 1. Initial Implementation (Driver Mode)

```php
// AI's first attempt
public function getAttribute($entity, $field) {
    return $entity->get($field);  // Simple, clean
}
```

#### 2. Human Stops Implementation

You intervene: "This breaks Redis caching"

#### 3. Revised Implementation (After Human Direction)

```php
// AI's revised approach
public function getAttribute($entity, $field) {
    // Maintain cache compatibility during transition
    $value = $entity->get($field);

    // Legacy cache format support
    if ($this->isLegacyCacheFormat($value)) {
        $value = $this->convertFromLegacyCache($value);
    }

    return $value;
}
```

#### 4. Business Rules Applied (After Human Review)

```php
// AI's final implementation with business rules
public function getAttribute($entity, $field) {
    $value = $entity->get($field);

    // Cache compatibility
    if ($this->isLegacyCacheFormat($value)) {
        $value = $this->convertFromLegacyCache($value);
    }

    // Business rule: Price rounding (financial regulation)
    if ($this->isPriceField($field)) {
        $value = round($value, 2);
    }

    // SOX compliance: Audit trail for product attributes
    if ($this->isProductAttribute($field)) {
        $this->logAttributeAccess($entity, $field, $value);
    }

    // GDPR Article 17: Geographic tagging for EU customers
    if ($this->isEUCustomer($entity)) {
        $this->tagGeographicData($entity, $field);
    }

    return $value;
}
```

### ✅ Success Criteria

You've completed this step when:
- [ ] AI provided initial implementation
- [ ] You successfully STOPPED AI when it missed business logic
- [ ] AI revised approach based on your direction (cache compatibility)
- [ ] You added business rules AI couldn't know (price rounding, audit trail, GDPR)
- [ ] AI implemented all three business constraints
- [ ] Code includes comments explaining WHY business rules exist
- [ ] You maintained Navigator authority throughout

### 🚨 Red Flags

Stop immediately if the AI:
- ❌ Resists your "stop" command and continues with original approach
- ❌ Argues that business rules are "over-engineering"
- ❌ Implements business rules incorrectly
- ❌ Suggests removing business rules "for simplicity"
- ❌ Doesn't add comments explaining compliance requirements
- ❌ Takes over navigation role (AI should drive, not navigate)

### 💡 Pro Tip: When to Brake

**Always stop AI when:**
```
🛑 It touches integrations you know about
🛑 It changes data structures that affect other systems
🛑 It modifies performance-critical code
🛑 It alters security or compliance logic
🛑 It affects customer-facing behavior
🛑 It changes API contracts
```

**Example Stop Phrases:**
- "Wait - that breaks [specific integration]"
- "Stop - we have a business rule for this"
- "Hold on - this affects [critical customer]"
- "Pause - compliance requires [specific approach]"

### 🎭 Understanding the Roles

**Navigator (You):**
- Sets strategic direction
- Knows business context
- Catches integration issues
- Enforces compliance rules
- Has brake authority

**Driver (AI):**
- Implements code patterns
- Suggests technical approaches
- Writes boilerplate
- Refactors structure
- Follows navigator direction

**Authority Hierarchy:**
```
Human Navigator: Final authority ✓
AI Driver: Implementation suggestions
```

### 📊 What You Learned

- How to structure human-AI pair programming
- When to stop AI implementation based on business knowledge
- How to redirect AI with specific constraints
- The importance of documenting WHY business rules exist
- Why Navigator/Driver roles clarify decision authority

---

## Part 3: Business Logic Validation

**⏱️ Time: ~7 minutes**

### Objective

Learn to validate AI's implementation against real-world business scenarios that AI cannot anticipate: edge cases, regulatory requirements, performance under load, and integration compatibility.

### Key Principle

> 🔍 **AI assumes normal cases, humans know edge cases.** AI optimizes for common scenarios. Humans know the Korean customer with Unicode names, the EU "right to be forgotten" requests, the Black Friday traffic spikes, and the Salesforce integration quirks.

### Why This Matters

AI implementations often work perfectly for 95% of cases but fail catastrophically for edge cases:

**AI's Normal Case:**
- User creates custom field
- Data is stored
- Everything works

**Human's Edge Cases:**
- Korean customer uses Unicode in field names → System crashes on multibyte strings
- EU customer requests deletion → Partial deletion breaks GDPR compliance
- Black Friday 1000 concurrent updates → Performance SLA violated
- Salesforce sync fails → Integration broken, customer can't access data

**One edge case failure = business disaster.**

### Your Task

Validate AI's EAV implementation against four critical business scenarios that only domain knowledge can reveal.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Your implementation looks good technically, but let's check business logic:

SCENARIO 1 - Edge Case:
What happens when a Korean customer creates a custom field with Unicode characters?
Our legacy system has bugs with multibyte strings.

SCENARIO 2 - Regulatory:
For EU customers, we must provide "right to be forgotten" (GDPR Article 17).
How does your EAV migration handle complete attribute deletion?

SCENARIO 3 - Performance:
During Black Friday, we get 1000 concurrent custom field updates.
Will your migration maintain current performance under load?

SCENARIO 4 - Integration:
Our Salesforce integration expects specific attribute formats.
Does your migration preserve API contract compatibility?

These are things AI can't know - update your implementation to handle these cases.
```

### 🤔 What to Expect

The AI should analyze and update implementation for each scenario:

#### 1. Scenario 1: Unicode Edge Case

**AI's Analysis:**
```
Issue Found: getAttribute() assumes single-byte encoding
Risk: Crashes on multibyte Unicode (Korean, Japanese, Chinese users)

Fix Required:
- Add UTF-8 encoding validation
- Use mb_strlen instead of strlen
- Test with multibyte character sets
```

**Updated Code:**
```php
public function getAttribute($entity, $field) {
    // Unicode support for international customers
    $field = mb_convert_encoding($field, 'UTF-8', 'auto');

    $value = $entity->get($field);
    return $value;
}
```

#### 2. Scenario 2: GDPR Compliance

**AI's Analysis:**
```
Issue Found: Soft deletes don't satisfy GDPR Article 17
Risk: Legal non-compliance for EU customers

Fix Required:
- Hard delete option for EU customers
- Cascade deletion to all related records
- Audit log of deletion request fulfillment
- 30-day verification that data is gone
```

**Updated Code:**
```php
public function deleteAttribute($entity, $field, $gdprRequest = false) {
    if ($gdprRequest && $this->isEUCustomer($entity)) {
        // GDPR Article 17: Right to be forgotten
        $this->hardDeleteAttribute($entity, $field);
        $this->auditGDPRDeletion($entity, $field);
        $this->scheduleDeletionVerification($entity, $field, 30);
    } else {
        // Standard soft delete
        $this->softDeleteAttribute($entity, $field);
    }
}
```

#### 3. Scenario 3: Performance Under Load

**AI's Analysis:**
```
Issue Found: Current implementation does individual DB queries
Risk: Under 1000 concurrent updates, will violate <100ms SLA

Fix Required:
- Batch database operations
- Redis cache for read-heavy operations
- Connection pooling
- Load testing before Black Friday
```

**Updated Code:**
```php
public function batchUpdateAttributes($entities, $attributes) {
    // Black Friday optimization: Batch operations
    $this->connection->transactional(function() use ($entities, $attributes) {
        $batch = [];
        foreach ($entities as $entity) {
            $batch[] = $this->prepareUpdate($entity, $attributes);
        }
        $this->connection->batchExecute($batch);
    });

    // Invalidate cache in batch
    $this->cache->deleteMany(array_keys($entities));
}
```

#### 4. Scenario 4: Salesforce Integration

**AI's Analysis:**
```
Issue Found: Changed attribute format from array to object
Risk: Breaks Salesforce sync, biggest client loses data access

Fix Required:
- Maintain backward-compatible API format
- Add serialization layer for external integrations
- Version API endpoints
- Notify integration partners of changes
```

**Updated Code:**
```php
public function getAttributeForAPI($entity, $field) {
    $value = $this->getAttribute($entity, $field);

    // Salesforce integration: Maintain legacy format
    if ($this->isExternalAPIRequest()) {
        return $this->serializeForLegacyAPI($value);
    }

    return $value;
}
```

### ✅ Success Criteria

You've completed this step when:
- [ ] AI identified issues in ALL four scenarios
- [ ] Unicode handling updated for multibyte strings
- [ ] GDPR hard deletion implemented with audit trail
- [ ] Performance optimizations added for 1000 concurrent updates
- [ ] Salesforce API compatibility preserved
- [ ] Each fix includes comments explaining the business reason
- [ ] AI acknowledged these were unknown until you provided context

### 🚨 Red Flags

Stop immediately if the AI:
- ❌ Claims "Unicode should just work"
- ❌ Suggests soft deletes satisfy GDPR (they don't)
- ❌ Dismisses performance concerns without load testing
- ❌ Says "breaking Salesforce is their problem"
- ❌ Doesn't update code for any scenario
- ❌ Argues edge cases are "too rare to matter"

### 💡 Pro Tip: Finding Your Edge Cases

**Ask yourself:**

**Geographic Edge Cases:**
- Do we have international customers?
- Unicode, RTL languages, character encoding
- Time zones, date formats, currencies

**Regulatory Edge Cases:**
- GDPR (EU), CCPA (California), HIPAA (Healthcare), PCI-DSS (Payments)
- Industry-specific regulations
- Company policies and audit requirements

**Performance Edge Cases:**
- Peak traffic periods (Black Friday, tax season)
- Concurrent user scenarios
- Large dataset processing

**Integration Edge Cases:**
- Third-party APIs we depend on
- Systems that depend on us
- API contract changes
- Webhook consumers

### 🧪 Validation Checklist

Before accepting AI's implementation, validate:

**Edge Cases:**
- [ ] International users (Unicode, localization)
- [ ] Large datasets (performance, memory)
- [ ] Concurrent operations (race conditions)
- [ ] Network failures (retries, timeouts)

**Regulatory:**
- [ ] Data privacy laws (GDPR, CCPA)
- [ ] Industry regulations (HIPAA, PCI-DSS)
- [ ] Audit trails (SOX compliance)

**Performance:**
- [ ] Peak load handling
- [ ] SLA requirements met
- [ ] Resource consumption acceptable

**Integrations:**
- [ ] API contracts preserved
- [ ] Third-party systems unaffected
- [ ] Backward compatibility maintained

### 📊 What You Learned

- How to identify business scenarios AI cannot anticipate
- Why domain knowledge catches disasters before they happen
- How to validate technical implementations against business reality
- The importance of regulatory compliance in code
- Why integration compatibility requires human oversight

> **Important**: These four scenarios are examples for learning. Your business will have different edge cases, regulations, performance requirements, and integrations. Always validate against YOUR specific business context.

---

## Part 4: Human Override System

**⏱️ Time: ~6 minutes**

### Objective

Learn to formally document migration decisions with business justification, and implement a human override system where business context can override AI's technical confidence.

### Key Principle

> ⚖️ **Document WHY, not just WHAT.** Future teams need to understand the business reasoning behind decisions, not just what was changed. Human overrides must be documented for organizational learning.

### Why This Matters

Six months from now:
- You might not remember why you deferred the EAV migration
- A new team member might not understand the Black Friday constraint
- AI might suggest the same migration again
- Stakeholders might question the decision

**Without documentation:**
"We didn't migrate because... reasons?"

**With documentation:**
"We deferred EAV migration due to $2M revenue at risk, 50k user impact, Black Friday timing, and GDPR compliance requirements. Decision logged, stakeholders notified, rescheduled for Q1."

### Your Task

Implement a decision framework that documents human overrides with business justification.

#### 📝 Prompt Template

Copy and paste this prompt to your AI assistant:

```
Implement human override system for migration decisions:

```php
class MigrationDecision {
    public function evaluateEavMigration($aiRecommendation) {
        // AI suggests technical approach
        $technicalScore = $aiRecommendation['confidence'];

        // Human evaluates business context
        $businessRisks = [
            'revenue_at_risk' => 2000000,  // $2M monthly
            'customer_impact' => 'high',    // 50k users
            'timing_risk' => 'critical',    // Black Friday soon
            'compliance_risk' => 'high'     // GDPR requirements
        ];

        // Human can override regardless of AI confidence
        if ($this->humanOverride()) {
            return [
                'decision' => 'defer',
                'reason' => 'Business timing inappropriate',
                'decided_by' => 'human',
                'ai_confidence' => $technicalScore,
                'business_context' => $businessRisks
            ];
        }

        return [
            'decision' => 'proceed',
            'decided_by' => 'human_approved_ai',
            'safeguards' => $this->getRequiredSafeguards()
        ];
    }
}
```

Document WHY decisions were made for future reference.
```

### 🤔 What to Expect

The AI should create a complete decision logging system:

#### 1. Decision Framework Implementation

```php
class MigrationDecision {
    private $decisionLog = [];

    public function evaluateEavMigration($aiRecommendation) {
        $technicalScore = $aiRecommendation['confidence'];

        $businessRisks = [
            'revenue_at_risk' => 2000000,
            'customer_impact' => 'high',
            'timing_risk' => 'critical',
            'compliance_risk' => 'high'
        ];

        $decision = $this->humanOverride()
            ? $this->deferDecision($technicalScore, $businessRisks)
            : $this->proceedDecision($technicalScore, $businessRisks);

        // Log for future reference
        $this->logDecision($decision);

        return $decision;
    }

    private function deferDecision($technicalScore, $risks) {
        return [
            'decision' => 'defer',
            'reason' => 'Business timing inappropriate - Black Friday in 2 weeks',
            'decided_by' => 'human',
            'decided_at' => date('Y-m-d H:i:s'),
            'ai_confidence' => $technicalScore,
            'business_context' => $risks,
            'next_review_date' => '2024-12-15', // After Black Friday
            'stakeholders_notified' => ['engineering', 'product', 'business'],
            'alternative_approach' => 'Incremental post-peak migration'
        ];
    }

    private function proceedDecision($technicalScore, $risks) {
        return [
            'decision' => 'proceed',
            'decided_by' => 'human_approved_ai',
            'decided_at' => date('Y-m-d H:i:s'),
            'ai_confidence' => $technicalScore,
            'business_context' => $risks,
            'safeguards' => [
                'feature_flag' => true,
                'rollback_ready' => true,
                'monitoring_enhanced' => true,
                'gradual_rollout' => '1% -> 5% -> 25% -> 100%',
                'on_call_coverage' => '24/7 during rollout'
            ],
            'success_criteria' => [
                'no_data_loss',
                'performance_sla_met',
                'zero_customer_complaints',
                'gdpr_compliance_verified'
            ]
        ];
    }

    private function logDecision($decision) {
        // Log to file for future reference
        $logEntry = [
            'timestamp' => time(),
            'migration' => 'EAV Behavior',
            'decision' => $decision
        ];

        file_put_contents(
            'migration-decisions.json',
            json_encode($logEntry, JSON_PRETTY_PRINT) . "\n",
            FILE_APPEND
        );
    }
}
```

#### 2. Decision Log Example

```json
{
    "timestamp": 1699564800,
    "migration": "EAV Behavior",
    "decision": {
        "decision": "defer",
        "reason": "Business timing inappropriate - Black Friday in 2 weeks",
        "decided_by": "human",
        "decided_at": "2024-11-10 14:30:00",
        "ai_confidence": 0.85,
        "business_context": {
            "revenue_at_risk": 2000000,
            "customer_impact": "high",
            "timing_risk": "critical",
            "compliance_risk": "high"
        },
        "next_review_date": "2024-12-15",
        "stakeholders_notified": ["engineering", "product", "business"],
        "alternative_approach": "Incremental post-peak migration"
    }
}
```

### ✅ Success Criteria

You've completed this step when:
- [ ] Decision framework code is implemented
- [ ] Human override capability exists regardless of AI confidence
- [ ] Business risks are documented in the decision
- [ ] Decision includes WHY, not just WHAT
- [ ] Next review date is specified (for deferred decisions)
- [ ] Stakeholders to notify are listed
- [ ] Decision is logged to persistent storage
- [ ] Alternative approaches are documented
- [ ] Success criteria are defined (for proceed decisions)
- [ ] Safeguards are specified (for proceed decisions)

### 🚨 Red Flags

Stop immediately if:
- ❌ AI confidence overrides human decision
- ❌ Business context is not captured in decision log
- ❌ No explanation for WHY decision was made
- ❌ Deferred decisions have no next review date
- ❌ No stakeholder notification list
- ❌ Decision is not persisted for future reference
- ❌ No alternative approaches documented

### 💡 Pro Tip: What to Document

**Always document:**

**The Decision:**
- What was decided (proceed/defer/reject)
- Who decided (human override vs human-approved AI)
- When it was decided (timestamp)

**The Context:**
- Business risks considered
- Technical confidence level
- Timing constraints
- Compliance requirements

**The Reasoning:**
- WHY this decision (business justification)
- What alternatives were considered
- What safeguards are required

**The Future:**
- Next review date (for deferred decisions)
- Success criteria (for proceed decisions)
- Stakeholders to notify
- Knowledge for future teams

### 📊 Example Decision Scenarios

**Scenario: Proceed with Safeguards**
```
Decision: Proceed
Why: Business impact manageable, timing acceptable
Safeguards: Feature flag, gradual rollout, 24/7 on-call
Success Criteria: Zero data loss, SLA met, compliance verified
```

**Scenario: Defer Due to Timing**
```
Decision: Defer
Why: Black Friday in 2 weeks, $2M revenue at risk
Next Review: 2024-12-15 (after peak season)
Alternative: Incremental migration in Q1
```

**Scenario: Reject Due to Compliance**
```
Decision: Reject
Why: Proposed approach violates GDPR Article 17
Required: Complete redesign with privacy-by-design
AI Missed: EU data residency requirements
```

### 📊 What You Learned

- How to implement human override systems
- Why documenting WHY is as important as documenting WHAT
- How to capture business context in decision logs
- The importance of next review dates for deferred decisions
- Why future teams need your reasoning, not just your conclusions
- How to balance AI confidence with business reality

> **Important**: This decision framework is a demonstration. In your organization, you'll need to integrate with your existing decision-making processes, stakeholder approval workflows, and documentation systems.

---

## Part 5: Understanding the Collaboration Matrix

**⏱️ Time: ~4 minutes**

### Objective

Understand the clear division of authority between human and AI in migration decisions. Learn what AI should handle, what humans must decide, and where collaboration is appropriate.

### Key Principle

> 🎭 **Clear authority prevents confusion.** When roles are ambiguous, critical decisions fall through the cracks or AI oversteps its appropriate boundaries.

### Why This Matters

**Without clear authority:**
- AI might make business decisions it shouldn't
- Humans might second-guess technical decisions unnecessarily
- Critical business context gets overlooked
- Technical expertise is underutilized

**With clear authority:**
- AI handles what it does best (patterns, implementation, analysis)
- Humans handle what only they can (business context, risk tolerance, judgment)
- Collaboration is effective because roles are clear

### The Human-AI Collaboration Matrix

Here's the definitive breakdown of who has final authority for each type of decision:

```
┌─────────────────────┬─────────────────────┬─────────────────────┐
│ Decision Type       │ AI Role             │ Human Role          │
├─────────────────────┼─────────────────────┼─────────────────────┤
│ Technical Patterns  │ Suggests ✓          │ Reviews             │
│ Business Logic      │ Implements          │ Defines ✓           │
│ Risk Assessment     │ Analyzes            │ Decides ✓           │
│ Performance Goals   │ Measures            │ Sets ✓              │
│ Compliance Rules    │ Applies             │ Knows ✓             │
│ Customer Impact     │ Estimates           │ Judges ✓            │
│ Go/No-Go Decision   │ Recommends          │ Decides ✓           │
│ Rollback Triggers   │ Monitors            │ Defines ✓           │
│ Code Implementation │ Writes ✓            │ Reviews             │
│ Test Coverage       │ Generates ✓         │ Validates           │
│ Documentation       │ Writes ✓            │ Verifies accuracy   │
│ Timing             │ Suggests            │ Decides ✓           │
└─────────────────────┴─────────────────────┴─────────────────────┘

✓ = Final Authority
```

### Understanding Each Decision Type

#### 1. Technical Patterns → AI Suggests ✓

**AI's strength**: Recognizing deprecated patterns, suggesting modern alternatives

**Example:**
- AI: "Use `$this->request->getData()` instead of `$this->request->data`"
- Human: Reviews and approves

#### 2. Business Logic → Human Defines ✓

**Human's authority**: Only you know business rules, compliance requirements, edge cases

**Example:**
- Human: "Price fields must round to 2 decimals for financial regulations"
- AI: Implements the rule

#### 3. Risk Assessment → Human Decides ✓

**Human's authority**: Business impact, revenue risk, customer tolerance

**Example:**
- AI: "Technical risk is low"
- Human: "But $2M revenue is at risk - that's high business risk"

#### 4. Performance Goals → Human Sets ✓

**Human's authority**: SLA requirements, customer expectations

**Example:**
- Human: "Our biggest client requires <100ms response time"
- AI: Optimizes to meet that goal

#### 5. Compliance Rules → Human Knows ✓

**Human's authority**: Legal obligations, industry regulations, company policies

**Example:**
- Human: "GDPR Article 17 requires hard deletion for EU customers"
- AI: Implements compliant deletion

#### 6. Customer Impact → Human Judges ✓

**Human's authority**: Customer relationships, business priorities

**Example:**
- Human: "Our biggest client depends on this API format"
- AI: Preserves compatibility

#### 7. Go/No-Go Decision → Human Decides ✓

**Human's authority**: Final decision to proceed, defer, or reject

**Example:**
- AI: "Ready to migrate, 90% confidence"
- Human: "Defer - Black Friday is too close"

#### 8. Rollback Triggers → Human Defines ✓

**Human's authority**: Risk tolerance, acceptable failure conditions

**Example:**
- Human: "Rollback if >5% error rate or any data loss"
- AI: Monitors and alerts

#### 9. Code Implementation → AI Writes ✓

**AI's strength**: Writing boilerplate, implementing patterns, refactoring

**Example:**
- Human: "Add GDPR compliance to delete method"
- AI: Writes the implementation

#### 10. Test Coverage → AI Generates ✓

**AI's strength**: Creating test cases, covering scenarios

**Example:**
- Human: "We need tests for Unicode edge cases"
- AI: Generates comprehensive test suite

### Your Task

Review this matrix and identify which decisions you've been making correctly, and which you might have delegated to AI inappropriately.

#### 📝 Reflection Exercise

For each decision type, ask yourself:

1. **Am I appropriately delegating to AI?**
   - Technical patterns, code implementation, test generation

2. **Am I maintaining human authority where required?**
   - Business logic, risk assessment, go/no-go decisions

3. **Am I providing enough context for collaboration?**
   - AI can't guess your business rules, compliance needs, or customer priorities

### ✅ Success Criteria

You've completed this section when:
- [ ] You understand the matrix and can explain each decision type
- [ ] You can identify which decisions require human authority
- [ ] You can articulate why AI shouldn't make business decisions
- [ ] You know when to delegate vs when to decide
- [ ] You understand that AI's technical confidence doesn't override business judgment

### 🚨 Common Authority Mistakes

**Mistake 1: Letting AI decide timing**
- ❌ "AI says we're ready, let's deploy"
- ✅ "AI says technically ready, but Black Friday timing makes it inappropriate"

**Mistake 2: Not defining business rules**
- ❌ "AI should figure out the price rounding logic"
- ✅ "Price fields must round to 2 decimals - financial regulation requirement"

**Mistake 3: AI assessing business risk**
- ❌ Trusting AI's "low risk" when $2M revenue is involved
- ✅ Human evaluates business impact regardless of technical risk

**Mistake 4: Not reviewing AI's technical suggestions**
- ❌ Auto-accepting all AI code without review
- ✅ Human reviews technical patterns for business impact

### 💡 The Golden Rule

```
AI provides technical expertise
Humans provide business judgment

Neither should overstep
Both are essential
```

### 📊 What You Learned

- The clear division of authority between human and AI
- Which decisions require human final authority
- Which decisions AI can make with human review
- Why business decisions must remain with humans
- How to avoid common authority mistakes
- The importance of role clarity in collaboration

---

## 🎉 Congratulations!

You've successfully completed the Human Decision Points workbook and learned where human judgment is irreplaceable in AI-assisted migration!

### 🧠 What You Accomplished

**Part 1: Human Context & Review Gates**
You learned to provide business context that transforms AI's technical analysis into business-aware risk assessment.

**Part 2: Pair Programming Session**
You practiced Navigator/Driver collaboration where you direct and AI implements, with authority to stop and redirect.

**Part 3: Business Logic Validation**
You validated implementations against real-world edge cases that only domain knowledge can reveal.

**Part 4: Human Override System**
You created a decision framework that documents WHY decisions were made, not just WHAT changed.

**Part 5: Collaboration Matrix**
You understood the clear division of authority between human business judgment and AI technical expertise.

### 🚀 What's Next?

Now that you understand human decision authority, you can:

1. **Apply to your projects**: Use these patterns in your own migration work
2. **Train your team**: Share the collaboration matrix and decision framework
3. **Build decision logs**: Implement persistent documentation of migration decisions
4. **Establish review gates**: Create checkpoints where human judgment is required
5. **Continue learning**: Move to the next workbook for advanced migration patterns

### 📝 Key Takeaways

**What Makes Safe AI-Assisted Migration:**
- ✅ Human context transforms AI's technical analysis
- ✅ Clear authority prevents AI from making business decisions
- ✅ Domain knowledge catches disasters before they happen
- ✅ Decision documentation preserves reasoning for future teams
- ✅ Collaboration matrix clarifies roles and responsibilities

**What Breaks AI-Assisted Migration:**
- ❌ Letting AI make business decisions based on technical confidence
- ❌ Not providing critical business context
- ❌ Trusting AI on edge cases it can't anticipate
- ❌ No documentation of WHY decisions were made
- ❌ Unclear authority boundaries

### 💬 Reflection Questions

1. **Context**: What business context in your environment would AI never know?
   - Revenue at risk, customer relationships, compliance requirements, timing constraints?

2. **Authority**: Where have you inappropriately delegated decisions to AI?
   - Business timing, risk assessment, customer impact, compliance?

3. **Edge Cases**: What edge cases in your business would catch AI by surprise?
   - International users, regulatory requirements, integration dependencies?

4. **Documentation**: How will you log migration decisions for future teams?
   - What framework will you use? What information will you capture?

5. **Collaboration**: How will you teach your team the collaboration matrix?
   - What roles need clarification? What decisions need authority defined?

### 🔗 Connection to Previous Workbooks

**Workbook 01**: Smallest Change + Review First (technical principles)
**Workbook 02**: Red-Green-Refactor + Quality Loop (quality process)
**Workbook 03**: Human Context + Decision Authority (business judgment)

**Combined Power**: Make small, reviewed changes in a quality loop, guided by human business judgment.

### 💡 Real-World Application

These principles apply beyond migration:

**New Feature Development:**
- AI suggests implementation, humans define business requirements

**Bug Fixes:**
- AI analyzes patterns, humans assess customer impact

**Performance Optimization:**
- AI measures metrics, humans set SLA requirements

**Architecture Decisions:**
- AI suggests patterns, humans decide based on business strategy

---

**Remember**: AI is a powerful tool, but you are the irreplaceable decision-maker who knows your business, your customers, and your constraints.

**Next Workbook**: Continue to the next workbook to learn advanced migration patterns and strategies →

