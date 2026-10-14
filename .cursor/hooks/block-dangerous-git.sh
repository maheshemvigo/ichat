#!/bin/bash
# beforeShellExecution — block destructive git commands unless user approves
input=$(cat)
command=$(python3 -c "import sys,json; print(json.load(sys.stdin).get('command',''))" <<< "$input" 2>/dev/null)

if [[ "$command" =~ git[[:space:]]+(push[[:space:]]+--force|reset[[:space:]]+--hard|clean[[:space:]]+-fd) ]]; then
  cat <<'EOF'
{
  "permission": "ask",
  "user_message": "This git command is destructive. Please confirm before continuing.",
  "agent_message": "Hook blocked a potentially destructive git command. Ask the user to confirm explicitly."
}
EOF
  exit 0
fi

echo '{ "permission": "allow" }'
exit 0
