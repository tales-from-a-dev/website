<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Markdown;

use App\Blog\Domain\Enum\BlogCategoryEnum;
use App\Blog\Domain\Exception\InvalidBlogPostException;
use App\Blog\Domain\ValueObject\BlogPost;

final class BlogPostFactory
{
    private const string FILENAME_PATTERN = '/^(?P<date>\d{4}-\d{2}-\d{2})-(?P<slug>[a-z0-9-]+)\.md$/';

    private const string DATE_FORMAT = '!Y-m-d';

    /**
     * @param array{
     *     title?: mixed,
     *     description?: mixed,
     *     tags?: mixed,
     *     category?: mixed,
     *     translation_key?: mixed,
     *     cover?: mixed,
     *     draft?: mixed,
     * } $frontmatter
     */
    public function create(string $path, string $locale, array $frontmatter): BlogPost
    {
        if (1 !== preg_match(self::FILENAME_PATTERN, basename($path), $matches)) {
            throw InvalidBlogPostException::invalidFilename($path);
        }

        $publishedAt = \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $matches['date']);
        if (false === $publishedAt || $publishedAt->format('Y-m-d') !== $matches['date']) {
            throw InvalidBlogPostException::invalidPublicationDate($path, $matches['date']);
        }

        $title = $this->stringOrNull($frontmatter['title'] ?? null);
        if (null === $title) {
            throw InvalidBlogPostException::missingTitle($path);
        }

        $slug = $matches['slug'];

        return new BlogPost(
            slug: $slug,
            publishedAt: $publishedAt,
            locale: $locale,
            title: $title,
            description: $this->stringOrNull($frontmatter['description'] ?? null),
            tags: $this->tags($frontmatter['tags'] ?? null),
            category: $this->category($path, $frontmatter['category'] ?? null),
            translationKey: $this->stringOrNull($frontmatter['translation_key'] ?? null) ?? $slug,
            cover: $this->stringOrNull($frontmatter['cover'] ?? null),
            draft: true === ($frontmatter['draft'] ?? false),
        );
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (!\is_string($value)) {
            return null;
        }

        $value = trim($value);

        return '' === $value ? null : $value;
    }

    /**
     * @return list<string>
     */
    private function tags(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, \is_string(...)));
    }

    private function category(string $path, mixed $value): BlogCategoryEnum
    {
        $value = $this->stringOrNull($value);
        if (null === $value) {
            throw InvalidBlogPostException::missingCategory($path);
        }

        $category = BlogCategoryEnum::tryFrom($value);
        if (null === $category) {
            throw InvalidBlogPostException::invalidCategory($path, $value);
        }

        return $category;
    }
}
