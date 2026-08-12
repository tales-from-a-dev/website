---
name: ci-check
description: Run the full CI pipeline locally, in the same order as .github/workflows/ci.yaml, so a green local run means a green CI run.
disable-model-invocation: true
---

# ci-check

Mirrors `.github/workflows/ci.yaml` exactly. Run it before pushing.

The workflow gets a clean container and a fresh cache for free; a local run does
not. The two preflight steps below exist to close that gap — skipping them is
what produces "passes locally, fails in CI" (and, for PHPStan, the reverse).

## Preflight

1. **Containers up.** Everything runs inside the `php` container.

   ```bash
   docker compose ps --status running --services | grep -qx php || make up
   ```

2. **Warm the dev cache.** PHPStan reads the compiled container XML from
   `var/cache/dev/`. A stale or absent cache yields confusing false positives
   about services that resolve fine at runtime.

   ```bash
   make cc
   ```

## Pipeline

Run in this order and **stop at the first failure** — later stages are usually
noise once an earlier one is broken.

| # | Command | CI equivalent | Notes |
|---|---|---|---|
| 1 | `make phpcsfixer-dry` | Run PHP CS Fixer | Style. Fix with `make phpcsfixer`. |
| 2 | `make twigcsfixer-dry` | Run Twig CS Fixer | Fix with `make twigcsfixer`. |
| 3 | `make phpstan` | Run PHPStan | Level 8, `--memory-limit 256M`. |
| 4 | `make test` | Run PHPUnit | Needs `make db-test` once, first run. |

CI runs step 4 as `make coverage` (Xdebug, slower). `make test` runs the same
suites without coverage — use it locally and only reach for `make coverage` when
you actually need the numbers.

## Reporting

Report the first failing stage and its output. Do not dump the output of stages
that passed — a bare "1–3 passed" line is enough.

For a style failure (1 or 2), just run the non-dry fixer and say what it
changed. Those are mechanical.

For PHPStan or PHPUnit, show the failing lines and diagnose. Do not add a
baseline entry to silence PHPStan unless the user asks — `make phpstan-baseline`
rewrites the whole baseline file and hides unrelated debt.

Note that `phpunit.xml.dist` enables `failOnWarning`, `failOnNotice`, and
`failOnDeprecation`: an undefined array key is a test failure here, not a yellow
marker. Treat it as a real bug in the code under test.

## First run on a clean checkout

The test database has to exist before the suite can run:

```bash
make db-test
```

If step 4 fails with a connection or missing-table error and this is a fresh
clone, that is the fix.
