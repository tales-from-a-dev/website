---
title: 'Why this blog has no database'
description: 'Posts live as markdown files committed to the repository, so publishing is a push and reviewing is a pull request.'
tags: ['symfony', 'php', 'markdown']
category: 'architecture'
translation_key: 'blog-without-a-database'
draft: false
---

This site already talks to Postgres for analytics and settings, so adding a `post` table would have been the path of least resistance. I went the other way: every post is a markdown file committed next to the code.

## The layout

Content lives under `content/blog/`, one directory per locale:

```
content/blog/
├── en/
│   └── 2026-08-12-why-this-blog-has-no-database.md
└── fr/
    └── 2026-08-12-pourquoi-ce-blog-na-pas-de-base-de-donnees.md
```

Two things are encoded in the path itself. The locale is the directory, so it is visible without opening the file, and two languages can never collide on the same slug. The publication date and the slug are the filename, so they are never duplicated in the front matter and never drift out of sync with it.

What is left in the front matter is only what the filename cannot carry:

```yaml
title: 'Why this blog has no database'
description: 'Shown as the meta description and og:description.'
tags: ['symfony', 'php', 'markdown']
category: 'architecture'
translation_key: 'blog-without-a-database'
```

That `translation_key` is the one field that earns its place. It links a post to its counterpart in the other locale, which is what lets the `hreflang` alternates point at the translated slug instead of blindly reusing the English one.

## What it buys

Writing happens in an editor, review happens in a pull request, and deploying is a push. There is no admin screen to build, no CRUD to maintain, no migration to write, and nothing to back up that git is not already keeping. When a post is wrong, `git revert` is the rollback.

## What it costs

Full-text search is no longer a `LIKE` away, and there is no scheduled publishing without a job that commits for me. Neither is worth a database table today, and both stay possible later: the read model is a value object, not a file handle, so swapping where posts come from is a repository change and nothing else.
