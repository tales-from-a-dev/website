---
name: new-module
description: Scaffold a new DDD module under src/ with its four layers, service container config, per-locale routes, templates, translations and test structure.
disable-model-invocation: true
---

# new-module

Creates a module under `src/<Module>/` wired the way every existing module is
wired. A module is not just a directory — it touches eight places, and a
half-finished one fails at runtime in ways that are annoying to trace (an
autowiring error, a route that 404s only in French, a template that renders a
raw translation key).

## Ask first

If not already supplied:

1. **Module name**, `UpperCamelCase` singular (`Article`, not `Articles`).
2. **Surface**: website (public, locale-prefixed), dashboard (admin,
   locale-agnostic), or both.
3. **URL prefix** per locale if website-facing, e.g. `en: '/blog'`,
   `fr: '/fr/blog'`. Root-level is `en: ''`, `fr: '/fr'`.
4. **Persistence**: does it need a Doctrine entity, or is it a read-only page
   like `Resume`?

## Build order

Work through these in order — later steps depend on earlier ones.

### 1. Layers

```
src/<Module>/
├── Domain/
│   ├── Entity/          # if persistent
│   ├── Enum/            # always: <Module>RouteNameEnum
│   ├── Repository/      # interface, if a Domain service consumes the repo
│   └── ValueObject/
├── Application/Message/  # only if async work is needed
├── Infrastructure/Repository/
├── Test/Factory/         # Foundry factory, if persistent
└── Ui/
    ├── Controller/{Website,Dashboard}/
    └── Form/{Data,Type}/
```

Create only the directories the module actually uses. Empty scaffolding
directories are worse than absent ones — they imply a shape that isn't there.

Templates for each file live in `templates/` next to this skill. They carry the
project's conventions already: `declare(strict_types=1)`, `final` (except
entities, which stay open for Doctrine proxies), Yoda conditions, single-action
`__invoke` controllers extending `App\Shared\Ui\Controller\AbstractController`.

### 2. Service config — `config/services/<module>.yaml`

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\<Module>\:
        resource: '../../src/<Module>/*'
```

Add a `parameters:` block and `bind:` entries only if the module reads env vars
(see `config/services/contact.yaml` for that shape).

### 3. Routes — `config/routes/<module>.yaml`

Website controllers get a **locale map**, never a plain string prefix. A plain
string makes the route English-only and silently breaks the `hreflang`
alternates in `base.html.twig`.

```yaml
website_<module>_controllers:
    resource:
        path: ../../src/<Module>/Ui/Controller/Website
        namespace: App\<Module>\Ui\Controller\Website
    type: attribute
    prefix:
        en: '/blog'
        fr: '/fr/blog'
    name_prefix: app_website_<module>_
    trailing_slash_on_root: false
```

Dashboard controllers stay locale-agnostic with a flat `/dashboard/<module>`
prefix and `app_dashboard_<module>_` name prefix.

### 4. Route name enum — `Domain/Enum/<Module>RouteNameEnum.php`

One backed case per route, matching the generated names exactly. Referenced
everywhere as `<Module>RouteNameEnum::WebsiteIndex->value` rather than a raw
string.

### 5. Templates — `templates/app/website/<module>/`

Website pages extend `app/base_website.html.twig`; dashboard pages extend
`app/base_dashboard.html.twig`. Both descend from `app/base.html.twig`, which
emits the `hreflang` alternates and the `meta_jsonld` block.

All text goes through `|trans`. Reuse components from `templates/component/`
(singular — AGENTS.md says `components/`, which is a typo) before writing new
markup.

### 6. Translations

Add keys to **both** `translations/en/messages+intl-icu.en.yaml` and
`translations/fr/messages+intl-icu.fr.yaml`. Form labels go in the `forms`
catalogue, flash messages in `alert`. Never add a key to one locale only.

### 7. Migration, if persistent

```bash
make db-diff      # generate from the new entity
make db-migrate   # apply
```

Review the generated SQL before applying. Never hand-write a migration file —
a PreToolUse hook blocks edits to applied ones.

### 8. Tests

Mirror the module structure across the suites that apply:

- `tests/Unit/<Module>/` — pure logic, no container
- `tests/Integration/<Module>/` — repositories and services against the DB,
  using a Foundry factory from `src/<Module>/Test/Factory/`
- `tests/Functional/` — a smoke test per public route

## Verify

```bash
make cc
make sf c='debug:router'                  # routes registered under both locales
make sf c='debug:container App\\<Module>' # services wired
make db-validate                          # if the module added an entity
```

Then run `/ci-check` before committing.
