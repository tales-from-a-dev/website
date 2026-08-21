<?php

declare(strict_types=1);

namespace App\Tests\Integration\Blog\Infrastructure\Repository;

use App\Blog\Domain\Enum\BlogCategoryEnum;
use App\Blog\Domain\Exception\UnreadableBlogPostException;
use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BlogPostRepositoryTest extends KernelTestCase
{
    private const string TEMPORARY_SLUG = 'temporary-post';

    private const string TEMPORARY_FILENAME = '2026-06-01-temporary-post.md';

    private BlogPostRepository $blogPostRepository;

    private string $contentDir;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->blogPostRepository = $container->get(BlogPostRepository::class);
        $this->contentDir = (string) $container->getParameter('app.blog_content_dir');
    }

    protected function tearDown(): void
    {
        $path = \sprintf('%s/en/%s', $this->contentDir, self::TEMPORARY_FILENAME);
        if (is_file($path)) {
            unlink($path);
        }

        parent::tearDown();
    }

    public function testItReturnsPublishedPostsNewestFirst(): void
    {
        self::assertSame(
            ['untranslated-post', 'second-post', 'first-post'],
            $this->slugsOf($this->blogPostRepository->findAll('en')),
        );
    }

    public function testItOmitsPostsMissingFromTheRequestedLocale(): void
    {
        self::assertSame(
            ['deuxieme-article', 'premier-article'],
            $this->slugsOf($this->blogPostRepository->findAll('fr')),
        );
    }

    public function testItReturnsNothingForALocaleWithoutContent(): void
    {
        self::assertSame([], $this->blogPostRepository->findAll('de'));
    }

    public function testItHidesDraftsOutsideDebug(): void
    {
        self::assertFalse(self::getContainer()->getParameter('kernel.debug'));
        self::assertNotContains('draft-post', $this->slugsOf($this->blogPostRepository->findAll('en')));
        self::assertNull($this->blogPostRepository->findOneBySlug('en', 'draft-post'));
    }

    public function testItCapsTheLatestPostsToTheRequestedLimit(): void
    {
        self::assertSame(
            ['untranslated-post', 'second-post'],
            $this->slugsOf($this->blogPostRepository->findLatest('en', 2)),
        );
    }

    public function testItReturnsEveryPostWhenTheLimitExceedsTheCorpus(): void
    {
        self::assertSame(
            $this->slugsOf($this->blogPostRepository->findAll('en')),
            $this->slugsOf($this->blogPostRepository->findLatest('en', 6)),
        );
    }

    public function testItFindsOnePostBySlug(): void
    {
        $post = $this->blogPostRepository->findOneBySlug('en', 'first-post');

        self::assertNotNull($post);
        self::assertSame('First post', $post->title);
        self::assertSame('2026-01-15', $post->publishedAt->format('Y-m-d'));
        self::assertSame('en', $post->locale);
        self::assertSame(['php', 'symfony'], $post->tags);
        self::assertSame('first-post', $post->translationKey);
    }

    public function testItReturnsNullForAnUnknownSlug(): void
    {
        self::assertNull($this->blogPostRepository->findOneBySlug('en', 'nope'));
    }

    public function testItRendersPostContent(): void
    {
        $post = $this->blogPostRepository->findOneBySlug('en', 'first-post');
        self::assertNotNull($post);

        $html = $this->blogPostRepository->findContent($post);

        self::assertStringContainsString('The oldest published post', $html);
        self::assertStringContainsString('<pre', $html);
        self::assertStringNotContainsString('title:', $html);
        self::assertSame($html, $this->blogPostRepository->findContent($post));
    }

    public function testItFindsPostsByCategory(): void
    {
        self::assertSame(
            ['first-post'],
            $this->slugsOf($this->blogPostRepository->findByCategory('en', BlogCategoryEnum::Architecture)),
        );
        self::assertSame(
            ['second-post'],
            $this->slugsOf($this->blogPostRepository->findByCategory('en', BlogCategoryEnum::Notes)),
        );
        self::assertSame([], $this->blogPostRepository->findByCategory('en', BlogCategoryEnum::Ai));
    }

    public function testItListsOnlyTheCategoriesHoldingAPublishedPost(): void
    {
        self::assertSame(
            [BlogCategoryEnum::Architecture, BlogCategoryEnum::Notes, BlogCategoryEnum::Performance],
            $this->blogPostRepository->findCategories('en'),
        );
        self::assertSame(
            [BlogCategoryEnum::Architecture, BlogCategoryEnum::Notes],
            $this->blogPostRepository->findCategories('fr'),
        );
    }

    public function testEveryCategoryYieldsAtLeastOnePost(): void
    {
        foreach ($this->blogPostRepository->findCategories('en') as $category) {
            self::assertNotEmpty($this->blogPostRepository->findByCategory('en', $category));
        }
    }

    public function testItThrowsWhenAPostFileIsMissing(): void
    {
        $post = new BlogPost(
            slug: 'gone',
            publishedAt: new \DateTimeImmutable('2026-01-01'),
            locale: 'en',
            title: 'Gone',
            description: null,
            tags: [],
            category: BlogCategoryEnum::Notes,
            translationKey: 'gone',
            cover: null,
            readingTime: 1,
        );

        $this->expectException(UnreadableBlogPostException::class);
        $this->expectExceptionMessage(\sprintf('Blog post file "%s/en/2026-01-01-gone.md" is missing or could not be read.', $this->contentDir));

        $this->blogPostRepository->findContent($post);
    }

    public function testItFindsPostsByTag(): void
    {
        self::assertSame(
            ['second-post', 'first-post'],
            $this->slugsOf($this->blogPostRepository->findByTag('en', 'symfony')),
        );
        self::assertSame([], $this->blogPostRepository->findByTag('en', 'draft-only'));
    }

    public function testEveryTagYieldsAtLeastOnePost(): void
    {
        $tags = $this->blogPostRepository->findTags('en');

        self::assertSame(['php', 'symfony'], $tags);

        foreach ($tags as $tag) {
            self::assertNotEmpty($this->blogPostRepository->findByTag('en', $tag));
        }
    }

    public function testItPicksUpANewPostWithoutAnyCacheClear(): void
    {
        $before = $this->blogPostRepository->findAll('en');

        $this->writeTemporaryPost('Temporary post');

        $after = $this->blogPostRepository->findAll('en');

        self::assertCount(\count($before) + 1, $after);
        self::assertSame(self::TEMPORARY_SLUG, $after[0]->slug);
        self::assertSame('Temporary post', $after[0]->title);
    }

    public function testItPicksUpAnEditedPostWithoutAnyCacheClear(): void
    {
        $this->writeTemporaryPost('Temporary post');

        $post = $this->blogPostRepository->findOneBySlug('en', self::TEMPORARY_SLUG);
        self::assertNotNull($post);
        self::assertSame('Temporary post', $post->title);
        self::assertStringContainsString('Temporary post body', $this->blogPostRepository->findContent($post));

        // the file count is unchanged, so only the mtime moves the fingerprint
        $this->writeTemporaryPost('Edited temporary post', 10);

        $edited = $this->blogPostRepository->findOneBySlug('en', self::TEMPORARY_SLUG);
        self::assertNotNull($edited);
        self::assertSame('Edited temporary post', $edited->title);
        self::assertStringContainsString('Edited temporary post body', $this->blogPostRepository->findContent($edited));
    }

    private function writeTemporaryPost(string $title, int $mtimeOffset = 0): void
    {
        $path = \sprintf('%s/en/%s', $this->contentDir, self::TEMPORARY_FILENAME);

        file_put_contents($path, <<<MARKDOWN
            ---
            title: '{$title}'
            tags: ['php']
            category: 'notes'
            ---

            {$title} body.
            MARKDOWN);

        if (0 !== $mtimeOffset) {
            touch($path, time() + $mtimeOffset);
        }
    }

    /**
     * @param list<BlogPost> $posts
     *
     * @return list<string>
     */
    private function slugsOf(array $posts): array
    {
        return array_map(static fn (BlogPost $post): string => $post->slug, $posts);
    }
}
