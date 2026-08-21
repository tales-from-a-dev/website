<?php

declare(strict_types=1);

namespace App\Blog\Domain\ValueObject;

use App\Blog\Domain\Enum\BlogCategoryEnum;

final readonly class BlogPost
{
    /**
     * @param list<string> $tags
     */
    public function __construct(
        public string $slug,
        public \DateTimeImmutable $publishedAt,
        public string $locale,
        public string $title,
        public ?string $description,
        public array $tags,
        public BlogCategoryEnum $category,
        public string $translationKey,
        public ?string $cover,
        public int $readingTime,
        public bool $draft = false,
    ) {
    }
}
