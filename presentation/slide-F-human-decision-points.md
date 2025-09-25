# Slide F: Human Decision Points
## Speaker Script & Demo Guide

### ⏱️ **TOTAL TIME: 12 minutes**
- Introduction: 1 minute
- Live Demo: 9 minutes (integrated flow)
- Collaboration Matrix: 1.5 minutes
- Transition: 30 seconds

### 🎯 Slide Objective
**Merges**: Slide 11 (Human Review Checkpoints) + Slide 16 (Migration Pair Programming)

Demonstrate when human judgment is critical and how to effectively pair program with AI for complex migration decisions.

### 📚 Context Files Required
- `migration-docs/PROJECT_AUDIT.md` - Business context
- `migration-docs/unknown_patterns/` - Complex patterns needing human insight
- `migration-docs/API.md` - Business logic to preserve

### 📋 Pre-Demo Setup
```bash
# Set up review environment
cd quickapps-cakephp3
mkdir -p human-decisions

# Open complex business logic file
code vendor/quickapps-plugins/eav/src/Model/Behavior/EavBehavior.php
```

---

## 🎤 Speaker Introduction (1 minute)

**SAY:** "AI is powerful, but humans are irreplaceable for business judgment. Let me show you exactly where human decisions matter most and how to pair program effectively with AI."

---

## 💻 Integrated Demo Flow (9 minutes)

### Step 1: Human Context Setting + Review Gates (2.5 minutes)

**SAY:** "Humans provide context AI cannot know"

**PROMPT 1A - Context Setting & Review:**
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

Reference migration-docs/unknown_patterns/PATTERN_005_EAV_MODEL.md for complexity.
```

### Step 2: Human-AI Pair Programming Session (3.5 minutes)

**SAY:** "Watch human-AI collaboration in action"

**PROMPT 2A - Pair Programming Flow:**
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

### Step 3: Business Logic Validation (2 minutes)

**SAY:** "Human domain knowledge catches what AI assumes"

**PROMPT 3A - Business Logic Check:**
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

### Step 4: Human Override System (1 minute)

**SAY:** "Humans must always have final authority"

**PROMPT 4A - Override Implementation:**
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

---

## 🎭 Human-AI Collaboration Matrix

**SHOW ON SLIDE:**
```
┌─────────────────────┬─────────────┬─────────────┐
│ Decision Type       │ AI Role     │ Human Role  │
├─────────────────────┼─────────────┼─────────────┤
│ Technical Patterns  │ Suggests ✓  │ Reviews     │
│ Business Logic      │ Implements  │ Defines ✓   │
│ Risk Assessment     │ Analyzes    │ Decides ✓   │
│ Performance Goals   │ Measures    │ Sets ✓      │
│ Compliance Rules    │ Applies     │ Knows ✓     │
│ Customer Impact     │ Estimates   │ Judges ✓    │
│ Go/No-Go           │ Recommends  │ Decides ✓   │
│ Rollback Triggers   │ Monitors    │ Defines ✓   │
└─────────────────────┴─────────────┴─────────────┘

✓ = Final Authority
```

---

## 🧠 What Humans Catch That AI Misses

**QUICK EXAMPLES:**
```
🔍 "This breaks our Salesforce integration"
🔍 "EU customers have different data rules"
🔍 "Black Friday timing is terrible"
🔍 "This violates our security policy"
🔍 "The CEO specifically uses this feature"
🔍 "Our biggest client depends on this performance"
```

---

## 💡 Key Takeaways (1.5 minutes)

**SAY THESE POINTS:**
1. **"Human context beats AI confidence"** - Business knowledge is irreplaceable
2. **"AI suggests, humans decide"** - Final authority always stays with humans
3. **"Domain knowledge prevents disasters"** - You know your business, AI doesn't
4. **"Document decisions for future teams"** - Context gets lost over time

---

## ⚖️ Decision Authority Principles

**FUNDAMENTAL RULES:**
```
✅ Humans define: Business rules, risk tolerance, timing
✅ AI handles: Implementation, patterns, optimization
✅ Together: Architecture, testing, validation
❌ Never: AI makes business decisions alone
```

---

## 🎬 Transition to Next Slide (30 seconds)

**SAY:** "Human judgment guides decisions. Now let's see how to capture and preserve that knowledge for future migrations..."

---

## 📚 Reference to Original Slides

**For detailed frameworks, see:**
- **Slide 11**: Complete human review checkpoint systems and templates
- **Slide 16**: Advanced pair programming patterns and role definitions
- **Decision templates**: Business context evaluation frameworks

---

## 🚨 Emergency Fallback

If technical issues occur:
1. Use whiteboard to draw human-AI decision flow
2. Show pre-written business context examples
3. Focus on principles rather than technical implementation
4. Role-play human override scenarios with audience