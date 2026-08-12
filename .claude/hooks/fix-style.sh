#!/usr/bin/env bash
#
# PostToolUse hook: auto-fix PHP / Twig coding style after every edit.
#
# The toolchain only exists inside the `php` container, so this shells in.
# It is deliberately non-blocking: if Docker is down, the file is outside the
# project, or the fixer errors, the hook exits 0 and the edit stands.
#
set -uo pipefail

root="${CLAUDE_PROJECT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)}"
cd "$root" || exit 0

file=$(jq -r '.tool_input.file_path // empty' 2>/dev/null)
[ -n "$file" ] || exit 0

# only touch files inside the project
case "$file" in
    "$root"/*) rel="${file#"$root"/}" ;;
    *) exit 0 ;;
esac

# never lint generated or vendored code
case "$rel" in
    vendor/* | var/* | public/assets/* | assets/vendor/* | migrations/*) exit 0 ;;
esac

# containers down -> nothing to do, stay silent
docker compose ps --status running --services 2>/dev/null | grep -qx php || exit 0

case "$rel" in
    *.php)
        docker compose exec -T php ./vendor/bin/php-cs-fixer fix "/app/$rel" --quiet >/dev/null 2>&1
        ;;
    *.twig)
        docker compose exec -T php ./vendor/bin/twig-cs-fixer lint --fix "/app/$rel" >/dev/null 2>&1
        ;;
esac

exit 0
