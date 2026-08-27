---
title: 'Markdown styleguide'
description: 'Every construct the markdown parser understands, rendered on a single page, so the prose styles can be checked at a glance.'
tags: ['markdown', 'design']
category: 'notes'
translation_key: 'markdown-styleguide'
draft: true
---

This page is a reference sheet, not an article. It renders every construct `tempest/markdown` understands, so the `.prose` styles can be checked in both themes after a design change without hunting through the archive for a post that happens to contain a table.

It carries `draft: true` in its front matter, which means the repository only lists it while `kernel.debug` is on. It is visible on the local blog index and absent from production.

```yaml
title: 'Markdown styleguide'
description: 'Shown as the meta description and og:description.'
tags: ['markdown', 'design']
category: 'notes'
translation_key: 'markdown-styleguide'
draft: true
```

## Headings

The post title is the only `h1` on the page, so a heading in the body starts at level two. Every heading gets an `id` slugged from its own text; appending a second run of hashes overrides it, which is what the anchor of the next section does.

### Third level

#### Fourth level

##### Fifth level

###### Sixth level

## Inline formatting ## inline

A paragraph carries **bold text**, *italic text*, ***both at once*** and ~~struck-through text~~ without any of them disturbing the line height. An inline `code span` sits at body size, and a highlighted one such as `{php}$post->publishedAt` keeps the colours of the code theme.

Links come in two shapes: an [internal one](/blog) and an [external one](*https://symfony.com), the second opening in a new tab because its target is prefixed with a star.

Inline HTML is passed through untouched, so <kbd>Ctrl</kbd> + <kbd>C</kbd> and <abbr title="Cascading Style Sheets">CSS</abbr> render as written.

## Lists

Unordered lists nest one level deep. A long item is the one to watch: the marker stays put on the first line while the text keeps its hanging indent.

- A short item
  - A nested item
  - Another nested item
- A longer item, wordy enough to wrap on a narrow viewport so the indent under the marker can be judged rather than guessed
- A last item

Ordered lists behave the same, and the numbering restarts inside a nested level.

1. Start the containers
   1. Drop and recreate the database
   2. Migrate and seed it
2. Build the assets
3. Watch the stylesheet

Nesting only holds under the **first** item of a list. Indent a child under any later item and the parser hoists it into the parent list, so a post with a deep list has to be flattened or rewritten as prose.

## Quotes

> A quote holds its own paragraph, and **inline formatting** keeps working inside it.
> A second line joins the same block rather than opening a new one.
> > A quote inside a quote nests one level further.

## Code

A fence carries an optional language and, after a space, an optional title. The title lands in a bare `.code-title` block above the code, which nothing in the stylesheet targets yet.

```php src/Blog/Domain/ValueObject/BlogPost.php
final readonly class BlogPost
{
    /**
     * @param list<string> $tags
     */
    public function __construct(
        public string $slug,
        public \DateTimeImmutable $publishedAt,
        public array $tags,
        public bool $draft = false,
    ) {
    }
}
```

```yaml config/services/blog.yaml
parameters:
    app.blog_content_dir: '%kernel.project_dir%/content/blog'
```

```twig
<twig:Post:PostContent>
    {{ content|raw }}
</twig:Post:PostContent>
```

```bash
make up
make asset-watch
```

A fence without a language is left unhighlighted, which is what tree diagrams and terminal output want.

```
content/blog/
├── en/
│   └── 2026-08-27-markdown-styleguide.md
└── fr/
```

A single line wider than the column has to scroll inside its own block instead of stretching the page.

```json
{"@context":"https://schema.org","@type":"BlogPosting","headline":"Why this blog has no database","datePublished":"2026-08-12","inLanguage":"en"}
```

## Diffs

A `diff` fence marks whole lines from their first character. The theme paints the full row rather than the glyph, and nothing underneath is highlighted, so the language is lost.

```diff
 public function findAll(string $locale): array
-    return $this->index($locale);
+    return array_values(array_filter($this->index($locale)));
```

Marking the changed lines inside a normal fence keeps the highlighting, which is what a before-and-after usually wants. Added lines go between <code>{+</code> and <code>+}</code>, removed ones between <code>{-</code> and <code>-}</code>, and the markers themselves are stripped from the output.

```php src/Blog/Infrastructure/Repository/BlogPostRepository.php
public function findAll(string $locale): array
{
{-    return $this->index($locale);-}
{+    return array_values(array_filter(
        $this->index($locale),
        fn (BlogPost $post): bool => !$post->draft || $this->debug,
    ));+}
}
```

## Tables

Cells accept inline formatting, and the header row is the one above the separator. Column alignment markers are parsed but ignored, so a `:---:` separator renders like any other.

| Front matter key  | Required | Falls back to           |
|-------------------|----------|-------------------------|
| `title`           | Yes      | Nothing, parsing fails  |
| `category`        | Yes      | Nothing, parsing fails  |
| `description`     | No       | The site meta description |
| `translation_key` | No       | The slug from the filename |
| `draft`           | No       | `false`                 |

## Rules

Both a thin rule and a thick one render the same separator.

---

===

## Images

An image on its own line is wrapped in a paragraph. Markdown holds a plain `src`, so it never passes through the asset pipeline: the placeholder below is inlined, and a real post would need a file served from a public path.

![A placeholder card reading 1200 by 600](data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjAwIDYwMCI+PHJlY3Qgd2lkdGg9IjEyMDAiIGhlaWdodD0iNjAwIiBmaWxsPSIjMTUyYTJmIi8+PHRleHQgeD0iNjAwIiB5PSIzMTgiIGZvbnQtZmFtaWx5PSJtb25vc3BhY2UiIGZvbnQtc2l6ZT0iNTIiIGZpbGw9IiM4ZmMwYzYiIHRleHQtYW5jaG9yPSJtaWRkbGUiPjEyMDAgJiMyMTU7IDYwMDwvdGV4dD48L3N2Zz4=)

## Containers

Three colons open a block and the word after them becomes its class. Nothing in the stylesheet targets these yet, so they render as a bare block and are the obvious hook for callouts.

:::note
A container holds its own paragraph and **inline formatting**.
:::

## Block HTML

Block-level HTML is emitted as written, which is how anything markdown has no syntax for gets in.

<details>
<summary>A collapsed section</summary>
<p>The content of the section, hidden until the summary is clicked.</p>
</details>
