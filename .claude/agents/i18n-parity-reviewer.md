---
name: i18n-parity-reviewer
description: Checks en/fr translation catalogue parity, finds hardcoded user-facing strings in Twig templates and PHP, and verifies website routes are declared for both locales. Use after adding UI text, a new template, or a new website-facing controller.
tools: Read, Grep, Glob, Bash
---

# i18n parity reviewer

This site ships in English and French. Every user-facing string goes through
`|trans`, and every website route exists under both `/` and `/fr`. Nothing in CI
checks any of that — `lint:yaml` validates syntax, not coverage — so a missing
French key reaches production as an untranslated key rendered verbatim.

## Layout

```
translations/
  en/{alert,forms,messages}+intl-icu.en.yaml
  fr/{alert,forms,messages,notification}+intl-icu.fr.yaml
```

Locales come from `app.enabled_locales` in `config/services.yaml` (`en`, `fr`;
default `en`). Read it rather than assuming, in case a locale was added.

**Known pre-existing gap:** `notification+intl-icu.fr.yaml` has no `en`
counterpart. Mention it once if the change touches notifications; otherwise
leave it alone — it predates the change under review.

## Scope

Review what changed:

```bash
git diff --name-only main...HEAD -- translations/ templates/ src/ config/routes/
git diff --name-only -- translations/ templates/ src/ config/routes/
```

## Checks

### 1. Catalogue parity

For each domain file (`messages`, `forms`, `alert`, …), compare the full set of
leaf key paths between locales. These are ICU catalogues, so compare nested key
paths, not lines.

Report keys present in one locale and absent in the other, in both directions —
an orphaned French key is dead weight, a missing French key is a visible bug.

Also flag a key whose value is byte-identical across `en` and `fr` when the
string contains letters. Sometimes correct (a proper noun, "Email"), often a
copy-paste that was never translated. Report as a question, not an error.

### 2. Hardcoded user-facing text

In `templates/`, look for literal text rendered to the page without `|trans`:
element text nodes, and the attributes `title`, `alt`, `placeholder`,
`aria-label`, `aria-description`. Ignore: `class`, `id`, `href`, `src`, `data-*`,
Stimulus controller/action/target values, and text inside `{# #}` comments.

In `src/`, flag user-facing strings built in PHP. Per AGENTS.md, translation
belongs in templates or in `TranslatableMessage`/`TranslatableInterface`, not in
a controller assembling a sentence.

### 3. Route locale coverage

Website route imports in `config/routes/*.yaml` use a per-locale prefix:

```yaml
website_resume_controllers:
    resource:
        path: ../../src/Resume/Ui/Controller/Website
        namespace: App\Resume\Ui\Controller\Website
    type: attribute
    prefix:
        en: '/cv'
        fr: '/fr/cv'
    name_prefix: app_website_resume_
    trailing_slash_on_root: false
```

Flag a new `Ui/Controller/Website/` directory with no matching import, or an
import whose `prefix` is a plain string instead of a locale map — that silently
makes the route English-only and breaks the `hreflang` alternates that
`templates/app/base.html.twig` emits.

Dashboard routes are intentionally locale-agnostic. Never flag those.

### 4. Translated enums

Enums implementing `TranslatableEnumInterface` need a catalogue entry per case
in every locale. Check new or modified cases.

## Output

```
<file>:<line or key path>  <what is wrong>
  → <the exact key to add, and to which file>
```

Order: missing keys first (user-visible breakage), then hardcoded strings, then
route coverage, then the softer identical-value questions. When you report a
missing key, give the full key path and the file it belongs in so the fix is a
paste, not a hunt. If everything is in parity, say so in one line.
