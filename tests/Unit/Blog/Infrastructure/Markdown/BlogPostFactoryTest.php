<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Markdown;

use App\Blog\Domain\Exception\InvalidBlogPostException;
use App\Blog\Infrastructure\Markdown\BlogPostFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BlogPostFactoryTest extends TestCase
{
    private const string PATH = '/app/content/blog/en/2026-08-12-why-this-blog-has-no-database.md';

    private BlogPostFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new BlogPostFactory();
    }

    public function testItCreatesBlogPostFromFilenameAndFrontmatter(): void
    {
        $blogPost = $this->factory->create(self::PATH, 'en', [
            'title' => 'Why this blog has no database',
            'description' => 'Posts live as markdown files.',
            'tags' => ['symfony', 'php'],
            'translation_key' => 'blog-without-a-database',
            'cover' => 'images/blog/cover.webp',
            'draft' => true,
        ]);

        self::assertSame('why-this-blog-has-no-database', $blogPost->slug);
        self::assertSame('2026-08-12 00:00:00', $blogPost->publishedAt->format('Y-m-d H:i:s'));
        self::assertSame('en', $blogPost->locale);
        self::assertSame('Why this blog has no database', $blogPost->title);
        self::assertSame('Posts live as markdown files.', $blogPost->description);
        self::assertSame(['symfony', 'php'], $blogPost->tags);
        self::assertSame('blog-without-a-database', $blogPost->translationKey);
        self::assertSame('images/blog/cover.webp', $blogPost->cover);
        self::assertTrue($blogPost->draft);
    }

    public function testItAppliesDefaultsWithOnlyATitle(): void
    {
        $blogPost = $this->factory->create(self::PATH, 'fr', ['title' => 'Un titre']);

        self::assertNull($blogPost->description);
        self::assertSame([], $blogPost->tags);
        self::assertSame('why-this-blog-has-no-database', $blogPost->translationKey);
        self::assertNull($blogPost->cover);
        self::assertFalse($blogPost->draft);
    }

    #[DataProvider('provideEmptyOptionalFields')]
    public function testItTreatsBlankOptionalFieldsAsAbsent(string $key): void
    {
        $blogPost = $this->factory->create(self::PATH, 'en', ['title' => 'Un titre', $key => '  ']);

        self::assertNull($blogPost->description);
        self::assertNull($blogPost->cover);
        self::assertSame('why-this-blog-has-no-database', $blogPost->translationKey);
    }

    #[DataProvider('provideUnusableTags')]
    public function testItKeepsOnlyStringTags(mixed $tags): void
    {
        $blogPost = $this->factory->create(self::PATH, 'en', ['title' => 'Un titre', 'tags' => $tags]);

        self::assertSame([], $blogPost->tags);
    }

    public function testItDiscardsNonStringTagsAndReindexesTheRest(): void
    {
        $blogPost = $this->factory->create(self::PATH, 'en', [
            'title' => 'Un titre',
            'tags' => ['symfony', 42, null, 'php'],
        ]);

        self::assertSame(['symfony', 'php'], $blogPost->tags);
    }

    #[DataProvider('provideNonBooleanDraftValues')]
    public function testItOnlyTreatsBooleanTrueAsADraft(mixed $draft): void
    {
        $blogPost = $this->factory->create(self::PATH, 'en', ['title' => 'Un titre', 'draft' => $draft]);

        self::assertFalse($blogPost->draft);
    }

    #[DataProvider('provideInvalidFilenames')]
    public function testItThrowsWhenTheFilenameDoesNotMatchThePattern(string $filename): void
    {
        $path = '/app/content/blog/en/'.$filename;

        $this->expectException(InvalidBlogPostException::class);
        $this->expectExceptionMessage(\sprintf('Blog post "%s" must be named YYYY-mm-dd-slug.md.', $path));

        $this->factory->create($path, 'en', ['title' => 'Un titre']);
    }

    public function testItThrowsWhenThePublicationDateDoesNotExist(): void
    {
        $path = '/app/content/blog/en/2026-02-31-a-post.md';

        $this->expectException(InvalidBlogPostException::class);
        $this->expectExceptionMessage(\sprintf('Blog post "%s" has an invalid publication date "2026-02-31".', $path));

        $this->factory->create($path, 'en', ['title' => 'Un titre']);
    }

    #[DataProvider('provideUnusableTitles')]
    public function testItThrowsWhenTheTitleIsMissing(array $frontmatter): void
    {
        $this->expectException(InvalidBlogPostException::class);
        $this->expectExceptionMessage(\sprintf('Blog post "%s" is missing a title in its front matter.', self::PATH));

        $this->factory->create(self::PATH, 'en', $frontmatter);
    }

    public static function provideEmptyOptionalFields(): iterable
    {
        yield 'description' => ['description'];
        yield 'cover' => ['cover'];
        yield 'translation key' => ['translation_key'];
    }

    public static function provideUnusableTags(): iterable
    {
        yield 'string' => ['symfony'];
        yield 'null' => [null];
        yield 'integers' => [[1, 2]];
    }

    public static function provideNonBooleanDraftValues(): iterable
    {
        yield 'false' => [false];
        yield 'null' => [null];
        yield 'string' => ['true'];
        yield 'integer' => [1];
    }

    public static function provideInvalidFilenames(): iterable
    {
        yield 'no date' => ['a-post.md'];
        yield 'partial date' => ['2026-08-a-post.md'];
        yield 'uppercase slug' => ['2026-08-12-A-Post.md'];
        yield 'accented slug' => ['2026-08-12-un-article-publié.md'];
        yield 'no slug' => ['2026-08-12.md'];
        yield 'wrong extension' => ['2026-08-12-a-post.markdown'];
    }

    public static function provideUnusableTitles(): iterable
    {
        yield 'absent' => [[]];
        yield 'empty' => [['title' => '']];
        yield 'blank' => [['title' => '   ']];
        yield 'not a string' => [['title' => 42]];
        yield 'null' => [['title' => null]];
    }
}
