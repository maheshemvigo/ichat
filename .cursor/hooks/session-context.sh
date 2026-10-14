#!/bin/bash
# sessionStart — remind agent of legacy stack constraints
cat <<'EOF'
{
  "additional_context": "London School Chat: Laravel 5.6, PHP 7.1+, Vue 2, MySQL 5.7. Read .cursor/AGENTS.md and .cursor/rules/ before coding. Do not use PHP 8+ or Vue 3 syntax."
}
EOF
exit 0
