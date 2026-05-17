# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> Full AI contribution guidelines (architecture, standards, anti-patterns) are in **AGENTS.md**. This file highlights the key points most relevant to Claude Code.

## Commands

All commands run inside Docker via `make`. The PHP container is the primary execution environment.

```bash
make up           # Start containers
make down         # Stop containers
make sh           # Shell into PHP container
```

**Testing:**
```bash
make test                          # All tests (testdox, no coverage)
make test f=tests/Unit/Foo/Bar.php # Single test file
make test-unit                     # Unit suite only
make test-integration              # Integration suite only
make test-functional               # Functional suite only
```

**Static analysis & linting:**
```bash
make phpstan      # PHPStan level 8 (--memory-limit 256M)
make phpcsfixer   # PHP CS Fixer (fix in place)
make twigcsfixer  # Twig CS Fixer (fix in place)
make rector       # Rector (apply refactors)
make linter       # Lint Twig/YAML + validate Doctrine mapping
```

**Database:**
```bash
make db           # Full reset: drop → create → migrate → fixtures → seed
make db-migrate   # Run pending migrations
make db-diff      # Generate migration from entity changes
make db-test      # Reset test database
```

**Assets:**
```bash
make asset-build  # Build Tailwind CSS
make asset-watch  # Watch and rebuild
```

**PHPUnit configuration notes:** `failOnWarning`, `failOnNotice`, and `failOnDeprecation` are all enabled. PHP warnings (e.g. undefined array key) will cause test failures, not just yellow markers.

## Architecture

The codebase follows **Domain-Driven Design** with a strict four-layer structure inside each module:

```
src/
├── Analytics/      # Page view tracking and traffic processing
├── Contact/        # Contact form
├── Experience/     # Professional timeline
├── GitHub/         # GitHub profile integration
├── Resume/         # CV page
├── Settings/       # Application settings
├── User/           # Authentication and user management
└── Shared/         # Cross-cutting: base entities, value objects, interfaces, kernel
```

Each module is split into:
- **Domain/** — Entities, value objects, repository interfaces, enums. No framework dependencies.
- **Application/** — Message classes and handlers for async processing (Symfony Messenger).
- **Infrastructure/** — Repository implementations, event listeners, HTTP clients, Doctrine types.
- **Ui/** — Controllers, form types, Twig components.

## Key patterns

**Entities** use Doctrine attribute mapping directly on constructor-promoted properties. `Clock::get()->now()` is used for `$createdAt` timestamps (allows clock mocking in tests).

**Repository interfaces** live in Domain, implementations in Infrastructure extending `ServiceEntityRepository`.

**Async processing** uses Symfony Messenger. Message classes sit in `Application/Message/`, handlers in the same namespace with `#[AsMessageHandler]`.

**Enums** are backed enums (often using `elao/enum`) used for routes (`ContactRouteNameEnum::WebsiteContact->value`), statuses, and entity fields.

**Array shapes** on `LogEntry::$extra` and similar PHPDoc annotations are enforced by PHPStan — keep them accurate and use `key?:` for optional fields.

**Ui layer** controllers use `renderBlock()` for Turbo Stream responses and fall back to redirects for plain requests, checking against `TurboBundle::STREAM_FORMAT`.

## Test organization

- `tests/Unit/` — Pure PHPUnit, no container. Mirror `src/` namespace structure.
- `tests/Integration/` — Hits the database; uses Zenstruck Foundry factories and DAMA doctrine rollback.
- `tests/Functional/` — Full HTTP via Zenstruck Browser.

PHPStan requires `var/cache/dev/` to be warmed up (`make cc` or run any Symfony command) before analysis, as it reads the compiled container XML.
