#!/usr/bin/env bash
set -euo pipefail

if ! command -v gcc >/dev/null 2>&1; then
  echo "gcc is not installed."
  exit 1
fi

mkdir -p build

modules=(
  "setup"
  "basics"
  "control flow"
  "functions arrays strings"
  "pointers memory"
  "structs files"
  "data structures algorithms"
  "recursion"
  "bitwise and cli"
  "file processing"
  "advanced topics"
  "mini projects"
  "capstone projects"
  "exercises"
  "solutions"
  "templates"
)

for module in "${modules[@]}"; do
  while IFS= read -r -d '' file; do
    name="$(basename "${file%.c}")"
    safe_name="${name// /_}"
    gcc "$file" -o "build/$safe_name"
    echo "built: build/$safe_name"
  done < <(find "$module" -type f -name "*.c" -print0)
done

echo "All module examples compiled into ./build"
