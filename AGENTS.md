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
| `Blog/` | Blog posts read from markdown files in `content/blog/<locale>/` |
| `Contact/` | Contact form + email |
| `Experience/` | Portfolio / experience entries (timeline) |
| `GitHub/` | GitHub API sync |
| `Resume/` | CV page (served under `/cv` and `/fr/cv`) |
| `Settings/` | Site settings |
| `User/` | Authentication |
| `Shared/` | Value objects, base classes, shared interfaces, SEO (JSON-LD encoder, structured data builders) |

Each module has its own services registered in `config/services/<module>.yaml` and routes in `config/routes/`.

Templates live in `templates/` with a parallel structure: `component/` for Twig Components, `app/` for page templates. Page templates are grouped per module (e.g. `templates/app/website/contact/index.html.twig`, `templates/app/website/shared/index.html.twig`).

Website pages extend `app/base_website.html.twig`, dashboard pages extend `app/base_dashboard.html.twig`; both of those extend `app/base.html.twig`, which owns the `hreflang` alternates and the `meta_jsonld` block. Standalone pages that want no surrounding chrome extend `app/base.html.twig` directly — the CV (`app/website/resume/index.html.twig`), the login screen and the error template do this.

Tests mirror this structure under `tests/Unit/`, `tests/Integration/`, and `tests/Functional/`.

## Internationalization

- Default locale: `en`. Enabled locales: `en`, `fr` (see `app.default_locale` / `app.enabled_locales` in `config/services.yaml`).
- Website routes are declared per-locale with French prefixed by `/fr` (e.g. `/contact` ↔ `/fr/contact`, `/cv` ↔ `/fr/cv`). Dashboard routes stay locale-agnostic.
- `templates/app/base.html.twig` emits `hreflang` alternates (including `x-default`) for every enabled locale on the current route.
- All user-facing strings go through `|trans`; translations live under `translations/{en,fr}/`.

## SEO

- `PrestaSitemapBundle` exposes the XML sitemap (`presta:sitemaps:dump` / `PrestaSitemapBundle_index` route). Alternate locales are emitted via `alternate.i18n: symfony`.
- `App\Analytics\Ui\Controller\Website\RobotController` serves `/robots.txt` and links the sitemap absolute URL.
- `App\Shared\Infrastructure\Seo\HomepageStructuredDataBuilder` produces the homepage `Person` JSON-LD; rendered in `templates/app/base.html.twig` via the `meta_jsonld` block.
- `App\Shared\Infrastructure\Serializer\Encoder\JsonLdEncoder` registers the `jsonld` format with the Symfony Serializer (HTML-safe encoding flags).

## Git and Pull Requests

### Commit Messages
- Use imperative mood: "Add feature" not "Added feature"
- First line: concise summary (50 chars max)
- Reference issues when applicable: "Fix #123"
- No period at end of subject line

### Branch Naming
- Feature: `feat/issue-<issue number>` (e.g., `feat/issue-123`)
- Bug fix: `fix/issue-<issue number>` (e.g., `fix/issue-123`)
- Epic: `epic/issue-<epic number>-<slug>` (e.g., `epic/issue-1441-search-filter`)
- Other: `tech/issue-000-<commit summary>` (e.g., `tech/issue-000-add-tests`)
- Use `main` for production code
- Use lowercase with dashes for branch names
- **Always create a branch** following the naming convention above before making any code changes; never commit directly to `main`
- **Never push directly to `main`**; all changes go through a branch and pull request

### Epics and Sub-Issues

Use an **epic** to plan a large feature that spans several pull requests. Open it from the **🗺️ Epic** issue template (`.github/ISSUE_TEMPLATE/epic.yml`); its title is prefixed `[Epic]: `.

An epic issue captures the *plan*, not the code. Fill each section:
- **Goal** — the outcome in a few sentences (what, not how).
- **Why** — the problem it solves and what prompted it.
- **Design** — the technical approach: the request/data flow, the key classes and files (reference existing code with paths), and any patterns being reused.
- **Scope decisions** — explicit in/out-of-scope calls and trade-offs (e.g. "classic GET submit, no LiveComponent").
- **Outcome** — the observable end state once every sub-issue is merged.
- **Sequencing** — the order sub-issues should land and their dependencies (foundation first).
- **Subtasks** — a checklist linking each sub-issue.
- **Risks** — known unknowns and cross-cutting concerns that could bite during execution.

Break the epic into small, independently reviewable **sub-issues**, each a User Story or Technical Story. Title them `<Epic short> <n>/<total> — <description>` (e.g. `Search page filter 3/5 — Apply search filters in the guest-session search builder`). Each sub-issue body opens with `Part of #<epic>` and follows **Goal / Context / Tasks / Acceptance criteria / Notes**, with concrete file paths.

Link every sub-issue to the epic as a **native GitHub sub-issue** (not just the checklist) so progress rolls up:

```bash
gh api --method POST repos/tales-from-a-dev/website/issues/<epic>/sub_issues \
  -F sub_issue_id="$(gh api repos/tales-from-a-dev/website/issues/<sub-issue> --jq .id)"
```

Give the epic and its sub-issues the same context label (e.g. `search`) plus `feature`/`tech`. Work the epic on one `epic/issue-<epic>-<slug>` branch and open a single PR that closes every sub-issue.

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
- Use components from `templates/component/` when available
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

## Agent Tooling

`.claude/` is committed and shared. It automates the conventions in this
document so they are enforced rather than merely remembered.

### Hooks (`.claude/settings.json`)

Both scripts shell into the `php` container, since the toolchain exists nowhere
else. They no-op silently when the stack is down.

| Hook | Event | Behaviour |
|---|---|---|
| `hooks/fix-style.sh` | after `Edit`/`Write` | Runs PHP CS Fixer or Twig CS Fixer on the edited file. Skips `vendor/`, `var/`, generated assets and `migrations/`. |
| `hooks/guard-protected-files.sh` | before `Edit`/`Write` | Refuses edits to already-applied migrations and to `.env*.local`. Creating a *new* migration file is still allowed. |

Style is therefore fixed on write. Don't hand-format to satisfy PHP CS Fixer —
`@Symfony:risky` already handles Yoda conditions, `declare(strict_types=1)`,
trailing commas and the blank line before `return`.

### Skills

| Skill | Use |
|---|---|
| `/ci-check` | Runs the CI pipeline locally in `ci.yaml` order, with the container-up and `make cc` preflight that CI gets for free. Run before pushing. |
| `/new-module` | Scaffolds a DDD module across all eight places it must touch, with templates under `.claude/skills/new-module/templates/`. |

### Subagents

| Agent | Use |
|---|---|
| `ddd-boundary-reviewer` | Layer boundaries, cross-module coupling, class naming. Carries an allow-list of accepted deviations — read it before "fixing" a reported non-issue. |
| `i18n-parity-reviewer` | `en`/`fr` catalogue parity, hardcoded user-facing strings, per-locale route coverage. |

Both review the diff, not the whole tree.

### MCP

`postgres-dev` (`.mcp.json`) gives read-only access to the dev database.
`compose.override.yaml` publishes Postgres on a random host port, so the wrapper
resolves the port and credentials from the running container at launch —
nothing is hardcoded and no password is stored in the repo. Requires `make up`.

## Anti-Patterns

Avoid these common mistakes:

- **Don't add typographic quotes** - Use straight quotes only (`'` and `"`)
- **Don't hardcode user-facing text** - Always use translations with `|trans`
- **Don't use `else` after `return`/`throw`** - Return/throw early instead
