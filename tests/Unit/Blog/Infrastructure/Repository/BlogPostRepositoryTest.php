<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Repository;

use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Markdown\BlogPostFactory;
use App\Blog\Infrastructure\Markdown\MarkdownRenderer;
use App\Blog\Infrastructure\Repository\BlogPostRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class BlogPostRepositoryTest extends TestCase
{
    private BlogPostRepository $blogPostRepository;

    protected function setUp(): void
    {
        $this->blogPostRepository = new BlogPostRepository(
            markdownRenderer: new MarkdownRenderer(),
            blogPostFactory: new BlogPostFactory(),
            blogCache: new ArrayAdapter(),
            blogContentDir: __DIR__.'/../../../../Fixtures/Blog/content',
            debug: true,
        );
    }

    public function testItRevealsDraftsInDebug(): void
    {
        $slugs = array_map(
            static fn (BlogPost $post): string => $post->slug,
            $this->blogPostRepository->findAll('en'),
        );

        self::assertContains('draft-post', $slugs);
        self::assertNotNull($this->blogPostRepository->findOneBySlug('en', 'draft-post'));
    }

    public function testItExposesDraftOnlyTagsInDebug(): void
    {
        self::assertContains('draft-only', $this->blogPostRepository->findTags('en'));
        self::assertNotEmpty($this->blogPostRepository->findByTag('en', 'draft-only'));
    }
}
