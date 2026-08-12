#!/usr/bin/env bash
#
# Launches a read-only Postgres MCP server against the dev database container.
#
# compose.override.yaml publishes the database as `ports: - "5432"`, i.e. Docker
# picks a random host port on every `up`. Hardcoding a connection string would
# break the first time the stack restarts, so the port and credentials are
# resolved here at launch. That also keeps the password out of the repo.
#
# MCP speaks JSON-RPC over stdout — every diagnostic here must go to stderr.
#
set -uo pipefail

root="${CLAUDE_PROJECT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)}"
cd "$root" || { echo "cannot cd to project root: $root" >&2; exit 1; }

if ! docker compose ps --status running --services 2>/dev/null | grep -qx database; then
    echo "The 'database' service is not running. Start it with 'make up' and reconnect this MCP server." >&2
    exit 1
fi

# "0.0.0.0:55001" / "[::]:55001" -> 55001
mapping=$(docker compose port database 5432 2>/dev/null | tail -1)
port="${mapping##*:}"
if [ -z "$port" ]; then
    echo "Could not resolve the published host port for database:5432." >&2
    exit 1
fi

# ask the container rather than parsing .env* (which holds real secrets)
env_of() { docker compose exec -T database printenv "$1" 2>/dev/null | tr -d '\r\n'; }

user=$(env_of POSTGRES_USER)
pass=$(env_of POSTGRES_PASSWORD)
db=$(env_of POSTGRES_DB)

: "${user:=app}"
: "${db:=app}"

# passwords routinely contain characters that are not URL-safe
enc_pass=$(jq -rn --arg v "$pass" '$v | @uri')

exec npx -y @modelcontextprotocol/server-postgres \
    "postgresql://${user}:${enc_pass}@127.0.0.1:${port}/${db}"
