# AI Contribution Guidelines

Welcome, AI assistant. Please follow these guidelines when contributing to this repository.

## Project Overview

Tales From a Dev is a portfolio website built with **Symfony 8.1** and **PHP 8.5+**, following **Domain-Driven Design (DDD)** principles with a layered architecture. The site is bilingual (English / French) with locale-prefixed routes and ships SEO primitives (sitemap, JSON-LD, robots).

## Stack

- **Backend**: PHP 8.5 / Symfony 8.1 / Doctrine ORM 3.5
- **Frontend**: Tailwind CSS 4.3 / Stimulus / Symfony AssetMapper / Twig Components / Turbo
- **Server**: FrankenPHP (Caddy-based, with Mercure built in)
- **Database**: PostgreSQL 17
- **Cache**: Valkey 9.0 (Redis-compatible)
- **Containerization**: Docker + Docker Compose + GNU Make

## Common Commands

All tasks run through `make`. The containers must be running first (`make up` or `make up-dev`).

```bash
# Start/stop
make up-dev          # Start with Xdebug
make stop

# Symfony console
make sf c='<cmd>'    # e.g. make sf c='debug:router'
make cc              # Clear cache

# Assets
make asset-watch     # Watch and rebuild Tailwind CSS
make asset-compile   # Compile for production

# Database
make db              # Create DB + run migrations + seed data
make db-migrate      # Run migrations only
make db-diff         # Generate migration from entity changes
make db-seed         # Seed fixtures

# Testing
make test                     # All tests
make test f='tests/Unit/...'  # Single file or directory
make test-unit
make test-functional
make test-integration

# Code quality
make fixer           # Fix PHP + Twig style
make phpstan         # Static analysis (Level 8)
make rector-dry      # Check Rector suggestions
make linter          # Lint Twig, YAML, validate Doctrine mapping
```

## Architecture

The app uses a **modular DDD structure** — each domain is self-contained under `src/`:

| Module | Purpose |
|--------|---------|
| `Analytics/` | Page view tracking + dynamic `robots.txt` |
| `Contact/` | Contact form + email |
| `Experience/` | Portfolio / experience entries (timeline) |
| `GitHub/` | GitHub API sync |
| `Resume/` | CV page (served under `/cv` and `/fr/cv`) |
| `Settings/` | Site settings |
| `User/` | Authentication |
| `Shared/` | Value objects, base classes, shared interfaces, SEO (JSON-LD encoder, structured data builders) |

Each module has its own services registered in `config/services/<module>.yaml` and routes in `config/routes/`.

Templates live in `templates/` with a parallel structure: `component/` for Twig Components, `app/` for page templates. Page templates are grouped per module (e.g. `templates/app/website/contact/index.html.twig`, `templates/app/website/shared/index.html.twig`).

Tests mirror this structure under `tests/Unit/`, `tests/Integration/`, and `tests/Functional/`.

## Internationalization

- Default locale: `en`. Enabled locales: `en`, `fr` (see `app.default_locale` / `app.enabled_locales` in `config/services.yaml`).
- Website routes are declared per-locale with French prefixed by `/fr` (e.g. `/contact` ↔ `/fr/contact`, `/cv` ↔ `/fr/cv`). Dashboard routes stay locale-agnostic.
- `base.html.twig` emits `hreflang` alternates (including `x-default`) for every enabled locale on the current route.
- All user-facing strings go through `|trans`; translations live under `translations/{en,fr}/`.

## SEO

- `PrestaSitemapBundle` exposes the XML sitemap (`presta:sitemaps:dump` / `PrestaSitemapBundle_index` route). Alternate locales are emitted via `alternate.i18n: symfony`.
- `App\Analytics\Ui\Controller\Website\RobotController` serves `/robots.txt` and links the sitemap absolute URL.
- `App\Shared\Infrastructure\Seo\HomepageStructuredDataBuilder` produces the homepage `Person` JSON-LD; rendered in `base.html.twig` via the `meta_jsonld` block.
- `App\Shared\Infrastructure\Serializer\Encoder\JsonLdEncoder` registers the `jsonld` format with the Symfony Serializer (HTML-safe encoding flags).

## PHP Code Standards

### Syntax and Style
- PHP 8.5+ syntax with constructor property promotion
- PSR-1, PSR-2, PSR-4, PSR-12 standards
- Yoda conditions: `if (null === $value)` (project convention)
- Strict comparisons only (`===`, `!==`)
- Braces required for all control structures
- Trailing commas in multi-line arrays
- Blank line before `return` (unless only statement in block)
- Don't add comments in classes as separators (e.g. `// === Methods for dashboards ===`)

### Naming
- Variables/methods: `camelCase`
- Config/routes/Twig: `snake_case`
- Constants: `SCREAMING_SNAKE_CASE`
- Classes: `UpperCamelCase`
- Abstract classes: `Abstract*` (except test cases)
- Interfaces: `*Interface`, Traits: `*Trait`, Exceptions: `*Exception`
- Most classes add a suffix showing its type:
  `*Controller`, `*Dto`, `*Event`, `*Subscriber`, `*Type`, `*Test`
- Templates/assets: `snake_case` (e.g., `detail_page.html.twig`)

### Class Organization
1. Properties before methods
2. Constructor first, then `setUp()`/`tearDown()` in tests
3. Method order: public, protected, private

### Code Practices
- Add `declare(strict_types=1);` to PHP files
- Mark PHP classes as final where possible
- Use enums (use `UpperCamelCase` for case names) instead of constants for fixed sets of values
- Avoid `else`/`elseif` after return/throw
- Use `sprintf()` for exception messages with `get_debug_type()` for class names
- Exception messages: capital letter start, period end, no backticks
- `return null;` for nullable, `return;` for void
- Always use parentheses when instantiating: `new Foo()`
- Comments: only for complex/unintuitive code, lowercase start, no period end
- Error messages: concise but precise and actionable (e.g. include class names, file paths)
- Handle exceptions explicitly (no silent catches)
- Config files in YAML format (`config/services/*.yaml`, `translations/<locale>/*.yaml`)
- Prefer project constants (Action::EDIT) over hardcoded strings

### PHPDoc
- No `@return` for void methods
- No single-line docblocks
- Group annotations by type
- `null` last in union types

## Templates (Twig)

- Modern HTML5 and Twig syntax
- All user-facing text via `|trans` filter (no hardcoded strings)
- Translation logic in templates, not PHP (use `TranslatableInterface`)
- Use components from `templates/components/` when available
- Accessibility: `aria-*` attributes, semantic tags, labels

## Testing

PHPUnit 12 + Zenstruck Browser (functional) + Zenstruck Foundry (factories) + DAMA transactional rollback.

Test environment uses `compose.test.yaml`. Run `make db-test` to prepare the test database before the first test run.

### Test Structure
- **Unit tests**: `tests/Unit/` - isolated component tests
- **Functional tests**: `tests/Functional/` - smoke tests
- **Integration tests**: `tests/Integration/` - integration tests

### Running Tests
- **Run all tests**: `make test`
- **Run a specific suite**:
    - Unit: `make test-unit`
    - Functional: `make test-functional`
    - Integration: `make test-integration`
- **Run a specific test file**:
    ```bash
    make test f=tests/Unit/SimpleTest.php
    ```
- **Code Coverage**: `make coverage` (requires Xdebug)

## Code Quality

- **PHPStan** at Level 8 with Symfony and Doctrine extensions (`phpstan.dist.neon`)
- **PHP CS Fixer** with Symfony + risky rules (`.php-cs-fixer.dist.php`)
- **Rector** targeting PHP 8.5 (`rector.php`)
- **Twig CS Fixer** (`.twig-cs-fixer.dist.php`)

CI runs all checks on every push/PR via `.github/workflows/ci.yaml`.

## Anti-Patterns

Avoid these common mistakes:

- **Don't add typographic quotes** - Use straight quotes only (`'` and `"`)
- **Don't hardcode user-facing text** - Always use translations with `|trans`
- **Don't use `else` after `return`/`throw`** - Return/throw early instead
