#!/bin/bash

# Batch commit and push all plugins with pending changes
# Usage: bash scripts/commit-push-plugins.sh [commit message]

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
PLUGINS_DIR="$SCRIPT_DIR/../plugins"
COMMIT_MSG="${1:-update plugin}"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m'

printf "${YELLOW}Batch processing plugins...${NC}\n"
echo ""

success_count=0
skip_count=0
error_count=0
declare -a failed_plugins=()
declare -a skipped_plugins=()

for plugin_dir in "$PLUGINS_DIR"/*; do
    [ -d "$plugin_dir" ] || continue
    plugin_name=$(basename "$plugin_dir")

    [ -d "$plugin_dir/.git" ] || continue

    printf "${GREEN}▶ $plugin_name${NC}\n"
    cd "$plugin_dir" || continue

    current_branch=$(git branch --show-current 2>/dev/null || echo "unknown")
    printf "  ${CYAN}Branch: $current_branch${NC}\n"

    # Check unpushed commits
    ahead_count=$(git rev-list --count @{u}..HEAD 2>/dev/null || echo "0")
    has_unpushed=false
    [ "$ahead_count" -gt 0 ] && has_unpushed=true && printf "  ${YELLOW}$ahead_count unpushed commit(s)${NC}\n"

    # Check working tree changes (including untracked files)
    has_changes=false
    [ -n "$(git status --porcelain 2>/dev/null)" ] && has_changes=true

    if [ "$has_changes" = false ] && [ "$has_unpushed" = false ]; then
        printf "  ${YELLOW}No changes, skipped${NC}\n"
        ((skip_count++))
        skipped_plugins+=("$plugin_name")
    else
        [ "$has_changes" = true ] && echo "  Changed files:" && git status -s | sed 's/^/    /'

        if [ "$has_changes" = true ]; then
            git add . 2>&1 | sed 's/^/    /'
            if ! git commit -m "$COMMIT_MSG" 2>&1 | sed 's/^/    /'; then
                printf "  ${RED}✗ Commit failed${NC}\n"
                ((error_count++))
                failed_plugins+=("$plugin_name (commit failed)")
                echo ""
                continue
            fi
        fi

        if git push origin "$current_branch" 2>&1; then
            printf "  ${GREEN}✓ Done${NC}\n"
            ((success_count++))
        else
            printf "  ${RED}✗ Push failed${NC}\n"
            ((error_count++))
            failed_plugins+=("$plugin_name (push failed)")
        fi
    fi
    echo ""
done

printf "${YELLOW}====================${NC}\n"
printf "${GREEN}Success: $success_count${NC}\n"
printf "${YELLOW}Skipped: $skip_count${NC}\n"
printf "${RED}Failed:  $error_count${NC}\n"
printf "${YELLOW}====================${NC}\n"

if [ ${#skipped_plugins[@]} -gt 0 ]; then
    printf "${YELLOW}Skipped plugins:${NC}\n"
    for p in "${skipped_plugins[@]}"; do printf "  ${YELLOW}-${NC} $p\n"; done
    echo ""
fi

if [ ${#failed_plugins[@]} -gt 0 ]; then
    printf "${RED}Failed plugins:${NC}\n"
    for p in "${failed_plugins[@]}"; do printf "  ${RED}✗${NC} $p\n"; done
    echo ""
fi
