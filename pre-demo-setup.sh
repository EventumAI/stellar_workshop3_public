#!/bin/bash
# Pre-Demo Setup Script for Slide D Presentation
# Run this 5 minutes before presentation starts
# Usage: bash pre-demo-setup.sh

set -e  # Exit on error

echo "=========================================="
echo "  SLIDE D PRE-DEMO SETUP VERIFICATION"
echo "=========================================="
echo ""

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check 1: Verify branch
echo "✓ Checking git branch..."
CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" = "item4" ]; then
    echo -e "${GREEN}✓ On correct branch: item4${NC}"
else
    echo -e "${RED}✗ WARNING: Not on item4 branch (currently on: $CURRENT_BRANCH)${NC}"
    echo "  Run: git checkout item4"
fi
echo ""

# Check 2: Count controllers
echo "✓ Counting controllers..."
CONTROLLER_COUNT=$(find quickapps-cakephp5/vendorCake3/quickapps-plugins/ -name "*Controller.php" 2>/dev/null | wc -l | tr -d ' ')
if [ "$CONTROLLER_COUNT" -eq 43 ]; then
    echo -e "${GREEN}✓ Found 43 controllers (correct)${NC}"
else
    echo -e "${YELLOW}⚠ Found $CONTROLLER_COUNT controllers (expected 43)${NC}"
fi
echo ""

# Check 3: Count plugins
echo "✓ Counting plugins..."
PLUGIN_COUNT=$(ls -la quickapps-cakephp5/vendorCake3/quickapps-plugins/ 2>/dev/null | grep "^d" | grep -v "^\." | wc -l | tr -d ' ')
if [ "$PLUGIN_COUNT" -ge 17 ]; then
    echo -e "${GREEN}✓ Found $PLUGIN_COUNT plugins (expected 17+)${NC}"
else
    echo -e "${YELLOW}⚠ Found $PLUGIN_COUNT plugins (expected 17+)${NC}"
fi
echo ""

# Check 4: Verify key documentation files
echo "✓ Verifying documentation files..."
DOCS_OK=true

if [ -f "migration-docs/CONTROLLERS_DEPENDENCY_MAP.md" ]; then
    echo -e "${GREEN}  ✓ CONTROLLERS_DEPENDENCY_MAP.md${NC}"
else
    echo -e "${RED}  ✗ CONTROLLERS_DEPENDENCY_MAP.md missing${NC}"
    DOCS_OK=false
fi

if [ -f "migration-docs/PLUGINS_DEPENDENCY_MAP.md" ]; then
    echo -e "${GREEN}  ✓ PLUGINS_DEPENDENCY_MAP.md${NC}"
else
    echo -e "${RED}  ✗ PLUGINS_DEPENDENCY_MAP.md missing${NC}"
    DOCS_OK=false
fi

if [ -f "migration-docs/DEPENDENCY_MATRIX.md" ]; then
    echo -e "${GREEN}  ✓ DEPENDENCY_MATRIX.md${NC}"
else
    echo -e "${RED}  ✗ DEPENDENCY_MATRIX.md missing${NC}"
    DOCS_OK=false
fi

if [ -f "migration-docs/test-plans/TEST_PLAN_LOCALE.md" ]; then
    echo -e "${GREEN}  ✓ TEST_PLAN_LOCALE.md${NC}"
else
    echo -e "${YELLOW}  ⚠ TEST_PLAN_LOCALE.md missing (optional)${NC}"
fi
echo ""

# Check 5: Verify key controller file
echo "✓ Verifying demo controller file..."
if [ -f "quickapps-cakephp5/vendorCake3/quickapps-plugins/locale/src/Controller/Admin/ManageController.php" ]; then
    echo -e "${GREEN}✓ LocaleController ManageController found${NC}"
    LINES=$(wc -l < "quickapps-cakephp5/vendorCake3/quickapps-plugins/locale/src/Controller/Admin/ManageController.php" | tr -d ' ')
    echo "  (Controller has $LINES lines)"
else
    echo -e "${RED}✗ LocaleController ManageController NOT FOUND${NC}"
fi
echo ""

# Check 6: Verify presentation guide
echo "✓ Checking presentation guide..."
if [ -f "PRESENTATION_SLIDE_D_GUIDE.md" ]; then
    echo -e "${GREEN}✓ PRESENTATION_SLIDE_D_GUIDE.md found${NC}"
else
    echo -e "${RED}✗ PRESENTATION_SLIDE_D_GUIDE.md missing${NC}"
fi
echo ""

# Summary
echo "=========================================="
echo "  SETUP SUMMARY"
echo "=========================================="
echo ""

if [ "$CURRENT_BRANCH" = "item4" ] && [ "$CONTROLLER_COUNT" -eq 43 ] && [ "$DOCS_OK" = true ]; then
    echo -e "${GREEN}✓ ALL CHECKS PASSED - Ready for demo!${NC}"
    echo ""
    echo "Quick reminders:"
    echo "  1. Have Claude Code open in terminal"
    echo "  2. Have PRESENTATION_SLIDE_D_GUIDE.md open in editor"
    echo "  3. Close unnecessary browser tabs"
    echo "  4. Test screen sharing if remote presentation"
    echo ""
    echo "Good luck with your presentation! 🚀"
else
    echo -e "${YELLOW}⚠ SOME CHECKS FAILED - Review above${NC}"
    echo ""
    echo "Please fix the issues before starting demo."
fi
echo ""

# Optional: Show quick stats
echo "=========================================="
echo "  QUICK STATS FOR REFERENCE"
echo "=========================================="
echo "Controllers: $CONTROLLER_COUNT"
echo "Plugins: $PLUGIN_COUNT"
echo "Branch: $CURRENT_BRANCH"
echo "Working Directory: $(pwd)"
echo ""

# List plugins for quick reference
echo "Plugins available for demo:"
ls quickapps-cakephp5/vendorCake3/quickapps-plugins/ 2>/dev/null | grep -v "total" | head -20 | sed 's/^/  - /'
echo ""

echo "=========================================="
echo "Setup check complete!"
echo "=========================================="
