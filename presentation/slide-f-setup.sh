#!/bin/bash
# Enhanced Pre-Demo Setup for Slide F: Human Decision Points

set -e  # Exit on error

echo "🔍 Slide F: Human Decision Points - Setup"
echo "=========================================="
echo ""

# 1. Verify working directory
if [ ! -d "migration-docs" ]; then
    echo "❌ ERROR: Run from W3 workshop root directory"
    echo "   Expected: /Users/cadukiz/Desktop/dev/workshops/W3/"
    echo "   Current: $(pwd)"
    exit 1
fi

echo "✅ Working directory verified"
echo ""

# 2. Verify all required documentation exists
echo "📋 Checking documentation files..."

REQUIRED_FILES=(
    "migration-docs/PROJECT_AUDIT.md"
    "migration-docs/API.md"
    "migration-docs/unknown_patterns/03_eav_implementation.md"
    "migration-docs/test-plans/TEST_PLAN_EAV.md"
)

ALL_FOUND=true
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file"
    else
        echo "  ❌ MISSING: $file"
        ALL_FOUND=false
    fi
done

if [ "$ALL_FOUND" = false ]; then
    echo ""
    echo "❌ ERROR: Missing required files"
    exit 1
fi

echo ""

# 3. Check Docker containers (optional but recommended)
echo "🐳 Checking Docker environment..."
if docker ps 2>/dev/null | grep -q "quickapps-web"; then
    echo "  ✅ CakePHP 3 container running"
else
    echo "  ⚠️  CakePHP 3 container not running (optional for this demo)"
    echo "     To start: docker-compose -f quickapps-cakephp3/docker-compose.yml up -d"
fi

if docker ps 2>/dev/null | grep -q "quickapps5-web"; then
    echo "  ✅ CakePHP 5 container running"
else
    echo "  ⚠️  CakePHP 5 container not running (optional for this demo)"
fi

echo ""

# 4. Prepare demo workspace
echo "📁 Setting up demo workspace..."
mkdir -p presentation/slide-f-demo
cd presentation/slide-f-demo

# 5. Create quick reference file for demo
cat > DEMO_QUICK_REFERENCE.md << 'EOF'
# Slide F Demo Quick Reference

## File Locations
- **EAV Implementation**: `../../migration-docs/unknown_patterns/03_eav_implementation.md`
- **EAV Test Plan**: `../../migration-docs/test-plans/TEST_PLAN_EAV.md`
- **Project Audit**: `../../migration-docs/PROJECT_AUDIT.md`
- **API Docs**: `../../migration-docs/API.md`

## Key Business Context (for prompts)

### Revenue & Scale
- Revenue at risk: **$2M/month**
- User records: **50,000**
- Performance SLA: **<100ms**

### Compliance & Regulations
- **GDPR**: EU customers (right to be forgotten - Article 17)
- **SOX**: Financial data audit trails
- **Data residency**: Geographic tagging for EU data

### Timing Constraints
- **Critical deadline**: Black Friday in 2 weeks
- **Zero tolerance** for production issues
- **Performance requirement**: 1000 concurrent updates sustained

### External Dependencies
- **Salesforce integration**: Specific attribute formats required
- **Redis caching**: 5-minute TTL, serialized data
- **API contracts**: Cannot break existing integrations

## Demo Flow & Timing

### Total Time: 13-15 minutes

1. **Introduction** (1 min)
   - "AI is powerful, but humans are irreplaceable for business judgment"

2. **Prompt 1A: Context Setting** (2.5-3 min)
   - Human provides business context AI cannot know
   - AI creates risk assessment
   - Highlight: $2M revenue, GDPR, Black Friday timing

3. **Prompt 2A: Pair Programming** (3.5-4 min)
   - Human navigates, AI drives implementation
   - Human interrupts to correct business logic
   - Iterative refinement with compliance rules

4. **Prompt 3A: Business Logic Validation** (2 min)
   - Four scenarios AI cannot infer
   - Unicode, GDPR, performance, integration
   - Domain knowledge prevents disasters

5. **Prompt 4A: Human Override System** (1-1.5 min)
   - Decision framework demonstration
   - Human authority over AI confidence
   - Documentation rationale

6. **Collaboration Matrix** (1.5 min)
   - Show decision authority table
   - AI suggests, humans decide
   - Final authority principles

7. **Transition** (30 sec)
   - "Human judgment guides decisions..."

## Key Teaching Points

### What Humans Provide That AI Cannot
- Historical context (past system bugs)
- Business timing (Black Friday constraints)
- Legal requirements (GDPR Article 17)
- Customer contracts (Salesforce integration)
- Revenue impact ($2M/month risk)
- Performance SLAs (internal agreements)

### Human-AI Collaboration Principles
1. **Human context beats AI confidence**
2. **AI suggests, humans decide**
3. **Domain knowledge prevents disasters**
4. **Document decisions for future teams**

### Decision Authority Matrix
```
Technical Patterns:  AI suggests ✓ | Human reviews
Business Logic:      AI implements  | Human defines ✓
Risk Assessment:     AI analyzes    | Human decides ✓
Performance Goals:   AI measures    | Human sets ✓
Compliance Rules:    AI applies     | Human knows ✓
Customer Impact:     AI estimates   | Human judges ✓
Go/No-Go:           AI recommends   | Human decides ✓
Rollback Triggers:   AI monitors    | Human defines ✓
```

## Audience Engagement Questions

### After Prompt 1A
- "Has anyone had a migration blocked by business timing?"
- "Who's dealt with GDPR compliance in technical decisions?"

### After Prompt 2A
- "What's been your experience pair programming with AI?"
- "Has AI ever missed a critical business rule in your projects?"

### After Prompt 3A
- "What business constraints have you needed to explain to AI?"
- "Any Salesforce integration horror stories?"

### After Prompt 4A
- "Who's comfortable letting AI make decisions without review?"
- "How do you document 'why' decisions were made?"

## Handling Technical Issues

### If AI Response Takes Too Long (>60s)
- Have pre-screenshot responses ready
- Explain: "AI response time varies - this is why we test"
- Continue with expected output discussion

### If AI Gives Unexpected Response
- Teaching moment: "This is exactly why human review matters"
- Ask audience: "What would you do with this response?"
- Redirect with follow-up question

### If Network Issues
- Switch to offline documentation review
- Walk through prompts conceptually
- Use whiteboard for decision flow diagram

## Pre-Demo Checklist

### 1 Hour Before
- [ ] Test internet connectivity
- [ ] Run all prompts once to verify behavior
- [ ] Screenshot successful responses as backup
- [ ] Open all documentation files
- [ ] Position backup slides
- [ ] Set timer alerts (10, 12, 14 min warnings)

### 15 Minutes Before
- [ ] Verify Claude Code connection
- [ ] Test screen sharing
- [ ] Open quick reference (this file)
- [ ] Clear Claude conversation history
- [ ] Have prompts ready to paste
- [ ] Take deep breath and relax!

## Success Metrics

### Demo Successful If:
- ✅ All prompts demonstrate human-AI collaboration
- ✅ Business context importance is clear
- ✅ Audience engaged with questions
- ✅ Decision authority principles understood
- ✅ Smooth transition to next section

### Key Takeaway for Audience:
**"AI is a powerful tool, but human judgment, business context, and domain expertise remain irreplaceable in migration decisions."**

---

**Good luck with your presentation! 🎤**
EOF

echo "  ✅ Created DEMO_QUICK_REFERENCE.md"

# 6. Create backup prompts file
cat > PROMPTS_BACKUP.md << 'EOF'
# Slide F: All Prompts (Backup Copy)

## Prompt 1A: Context Setting & Review Gates

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

## Prompt 2A: Pair Programming Flow

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

## Prompt 3A: Business Logic Validation

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

## Prompt 4A: Human Override System

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

**Note**: These prompts are ready to copy/paste during the presentation.
EOF

echo "  ✅ Created PROMPTS_BACKUP.md"

# 7. Create timing tracker
cat > TIMING_TRACKER.md << 'EOF'
# Presentation Timing Tracker

Use this during practice runs to track actual timing:

## Run 1 (Date: ________)
- Introduction: _____ min
- Prompt 1A: _____ min
- Prompt 2A: _____ min
- Prompt 3A: _____ min
- Prompt 4A: _____ min
- Collaboration Matrix: _____ min
- Transition: _____ min
- **Total**: _____ min

## Run 2 (Date: ________)
- Introduction: _____ min
- Prompt 1A: _____ min
- Prompt 2A: _____ min
- Prompt 3A: _____ min
- Prompt 4A: _____ min
- Collaboration Matrix: _____ min
- Transition: _____ min
- **Total**: _____ min

## Run 3 (Final - Date: ________)
- Introduction: _____ min
- Prompt 1A: _____ min
- Prompt 2A: _____ min
- Prompt 3A: _____ min
- Prompt 4A: _____ min
- Collaboration Matrix: _____ min
- Transition: _____ min
- **Total**: _____ min

## Notes
- Average AI response time: _____ seconds
- Longest section: _____________
- Sections needing cut: _____________
- Sections with good pacing: _____________
EOF

echo "  ✅ Created TIMING_TRACKER.md"

# 8. Open relevant documentation (if editor available)
echo ""
if command -v code &> /dev/null; then
    echo "📂 Opening files in VS Code..."
    code ../../migration-docs/unknown_patterns/03_eav_implementation.md &
    code ../../migration-docs/PROJECT_AUDIT.md &
    code DEMO_QUICK_REFERENCE.md &
    sleep 2
    echo "  ✅ Files opened in VS Code"
else
    echo "⚠️  VS Code not found - open files manually:"
    echo "   - migration-docs/unknown_patterns/03_eav_implementation.md"
    echo "   - migration-docs/PROJECT_AUDIT.md"
    echo "   - presentation/slide-f-demo/DEMO_QUICK_REFERENCE.md"
fi

# 9. Final status
echo ""
echo "================================================"
echo "✅ Setup Complete!"
echo "================================================"
echo ""
echo "Demo workspace: presentation/slide-f-demo/"
echo ""
echo "Files created:"
echo "  📄 DEMO_QUICK_REFERENCE.md - Quick reference for presentation"
echo "  📄 PROMPTS_BACKUP.md - All prompts ready to paste"
echo "  📄 TIMING_TRACKER.md - Track practice run timing"
echo ""
echo "Next steps:"
echo "  1. Review DEMO_QUICK_REFERENCE.md"
echo "  2. Practice with PROMPTS_BACKUP.md"
echo "  3. Time your run using TIMING_TRACKER.md"
echo "  4. Test all prompts with Claude 1-2 hours before presentation"
echo ""
echo "Good luck! 🎤"
echo ""
