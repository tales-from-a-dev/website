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

## Committed tooling

`.claude/` and `.mcp.json` are checked in and apply to every session. Full detail in AGENTS.md → *Agent Tooling*.

**Two hooks are active.** After every `Edit`/`Write`, `.claude/hooks/fix-style.sh` runs PHP CS Fixer or Twig CS Fixer on that file inside the `php` container — so **don't hand-format for style**; write the logic and let the fixer apply Yoda conditions, `declare(strict_types=1)`, trailing commas and blank-line-before-`return`. Before every `Edit`/`Write`, `.claude/hooks/guard-protected-files.sh` blocks edits to applied migrations (change the entity and run `make db-diff` instead) and to `.env*.local`. Both no-op when containers are down, so a passing edit is not proof the style was fixed — run `make up` first if it matters.

**Skills:** `/ci-check` runs the CI pipeline locally in `ci.yaml` order with the preflight CI gets for free — use it before pushing. `/new-module` scaffolds a DDD module across all eight places it touches.

**Subagents:** `ddd-boundary-reviewer` (layer boundaries, cross-module coupling, naming) and `i18n-parity-reviewer` (`en`/`fr` parity, hardcoded strings, per-locale routes). Both review the diff. The DDD agent has an explicit allow-list of accepted deviations — Doctrine `repositoryClass` imports, `elao/enum`, `Clock`, Security interfaces in `Domain/` are all deliberate, so don't "fix" them.

**MCP:** `postgres-dev` gives read-only access to the dev database; requires `make up`, since it resolves Postgres' randomly-assigned host port from the running container.

## Architecture

The codebase follows **Domain-Driven Design** with a strict four-layer structure inside each module:

```
src/
├── Analytics/      # Page view tracking and traffic processing
├── Blog/           # Markdown posts read from content/blog/<locale>/
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

**Entities** map Doctrine attributes directly onto public typed properties, and stay non-`final` so Doctrine can proxy them. Constructor promotion is used only where the entity has required construction arguments (`PageView`); `Experience`, `Settings` and `User` declare plain public properties. `Clock::get()->now()` is used for `$createdAt` timestamps (allows clock mocking in tests).

**Repositories** are implemented in `Infrastructure/Repository/` extending `ServiceEntityRepository`. A matching Domain interface is the exception, not the rule — only `Analytics` has one (`PageViewRepositoryInterface`). Add one when a `Domain/` service consumes the repository, since that is where the indirection actually buys decoupling; otherwise inject the concrete class as the existing modules do.

**Async processing** uses Symfony Messenger. Message classes sit in `Application/Message/`, handlers in the same namespace with `#[AsMessageHandler]`.

**Enums** are backed enums (often using `elao/enum`) used for routes (`ContactRouteNameEnum::WebsiteContact->value`), statuses, and entity fields.

**Array shapes** on `LogEntry::$extra` and similar PHPDoc annotations are enforced by PHPStan — keep them accurate and use `key?:` for optional fields.

**Ui layer** controllers use `renderBlock()` for Turbo Stream responses and fall back to redirects for plain requests, checking against `TurboBundle::STREAM_FORMAT`.

**Localization.** Default locale `en`, enabled locales `en` + `fr` (see `config/services.yaml`). Website routes are declared per-locale in `config/routes/*.yaml` with French prefixed by `/fr` (e.g. `/contact` ↔ `/fr/contact`, `/cv` ↔ `/fr/cv`); dashboard routes stay locale-agnostic. All user-facing text goes through `|trans` — translations live under `translations/{en,fr}/`. `templates/app/base.html.twig` emits `hreflang` alternates including `x-default` for every enabled locale. Website pages extend `app/base_website.html.twig`, dashboard pages extend `app/base_dashboard.html.twig`; both of those extend `app/base.html.twig`. Chrome-less pages (the CV, login, error) extend `app/base.html.twig` directly.

**SEO.** `PrestaSitemapBundle` exposes the XML sitemap (alternates configured via `alternate.i18n: symfony` in `config/packages/sitemap.yaml`). `App\Analytics\Ui\Controller\Website\RobotController` serves `/robots.txt`. JSON-LD structured data is built by `App\Shared\Infrastructure\Seo\HomepageStructuredDataBuilder` and serialized through `App\Shared\Infrastructure\Serializer\Encoder\JsonLdEncoder` (registered with format `jsonld`, HTML-safe flags). Templates render it via the `meta_jsonld` block in `templates/app/base.html.twig`.

## Test organization

- `tests/Unit/` — Pure PHPUnit, no container. Mirror `src/` namespace structure.
- `tests/Integration/` — Hits the database; uses Zenstruck Foundry factories and DAMA doctrine rollback.
- `tests/Functional/` — Full HTTP via Zenstruck Browser.

PHPStan requires `var/cache/dev/` to be warmed up (`make cc` or run any Symfony command) before analysis, as it reads the compiled container XML.
