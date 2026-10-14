#!/bin/bash
# afterFileEdit — lint edited PHP files when possible
input=$(cat)
file_path=$(python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('file_path',''))" <<< "$input" 2>/dev/null)

if [[ -z "$file_path" ]]; then
  exit 0
fi

if [[ "$file_path" != *.php ]]; then
  exit 0
fi

if ! command -v php >/dev/null 2>&1; then
  exit 0
fi

if [[ ! -f "$file_path" ]]; then
  exit 0
fi

output=$(php -l "$file_path" 2>&1)
if [[ $? -ne 0 ]]; then
  escaped=$(echo "$output" | python3 -c "import sys,json; print(json.dumps(sys.stdin.read()))")
  cat <<EOF
{
  "additional_context": "PHP syntax check failed for ${file_path}: ${escaped}"
}
EOF
fi

exit 0
