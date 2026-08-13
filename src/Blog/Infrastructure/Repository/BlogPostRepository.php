<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Exception\UnreadableBlogPostException;
use App\Blog\Domain\ValueObject\BlogPost;
use App\Blog\Infrastructure\Markdown\BlogPostFactory;
use App\Blog\Infrastructure\Markdown\MarkdownRenderer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class BlogPostRepository
{
    private const string FILE_PATTERN = '*.md';

    public function __construct(
        private MarkdownRenderer $markdownRenderer,
        private BlogPostFactory $blogPostFactory,
        #[Autowire(service: 'blog.cache')]
        private CacheInterface $blogCache,
        private string $blogContentDir,
        #[Autowire(value: '%kernel.debug%')]
        private bool $debug,
    ) {
    }

    /**
     * @return list<BlogPost>
     */
    public function findAll(string $locale): array
    {
        return array_values(array_filter(
            $this->index($locale),
            fn (BlogPost $post): bool => !$post->draft || $this->debug,
        ));
    }

    /**
     * @return list<BlogPost>
     */
    public function findByTag(string $locale, string $tag): array
    {
        return array_values(array_filter(
            $this->findAll($locale),
            static fn (BlogPost $post): bool => \in_array($tag, $post->tags, true),
        ));
    }

    public function findOneBySlug(string $locale, string $slug): ?BlogPost
    {
        foreach ($this->findAll($locale) as $post) {
            if ($post->slug === $slug) {
                return $post;
            }
        }

        return null;
    }

    public function findOneByTranslationKey(string $locale, string $translationKey): ?BlogPost
    {
        foreach ($this->findAll($locale) as $post) {
            if ($post->translationKey === $translationKey) {
                return $post;
            }
        }

        return null;
    }

    public function findContent(BlogPost $post): string
    {
        $path = \sprintf(
            '%s/%s/%s-%s.md',
            $this->blogContentDir,
            $post->locale,
            $post->publishedAt->format('Y-m-d'),
            $post->slug,
        );

        $mtime = is_file($path) ? filemtime($path) : false;
        if (false === $mtime) {
            throw UnreadableBlogPostException::atPath($path);
        }

        $key = \sprintf('blog.html.%s.%s.%d', $post->locale, $post->slug, $mtime);

        return $this->blogCache->get($key, fn () => $this->markdownRenderer->parse($this->read($path))->html);
    }

    /**
     * @return list<string>
     */
    public function findTags(string $locale): array
    {
        $tags = [];

        foreach ($this->findAll($locale) as $post) {
            foreach ($post->tags as $tag) {
                if (!\in_array($tag, $tags, true)) {
                    $tags[] = $tag;
                }
            }
        }

        sort($tags);

        return $tags;
    }

    /**
     * Drafts are cached with everything else so that the key stays environment
     * agnostic; visibility is applied on read instead.
     *
     * @return list<BlogPost>
     */
    private function index(string $locale): array
    {
        $key = \sprintf('blog.index.%s.%s', $locale, $this->fingerprint($locale));

        return $this->blogCache->get($key, fn () => $this->parse($locale));
    }

    /**
     * @return list<BlogPost>
     */
    private function parse(string $locale): array
    {
        $posts = [];

        foreach ($this->files($locale) as $file) {
            $path = $file->getPathname();

            $posts[] = $this->blogPostFactory->create(
                $path,
                $locale,
                $this->markdownRenderer->parse($this->read($path))->frontmatter,
            );
        }

        usort($posts, static fn (BlogPost $a, BlogPost $b): int => $b->publishedAt <=> $a->publishedAt ?: $a->slug <=> $b->slug);

        return $posts;
    }

    /**
     * Statting every file is cheap, parsing them is not: the fingerprint moves
     * as soon as a post is added, removed or edited, so the index invalidates
     * itself on deploy without a cache:pool:clear.
     */
    private function fingerprint(string $locale): string
    {
        $mtime = 0;
        $count = 0;

        foreach ($this->files($locale) as $file) {
            $mtime = max($mtime, $file->getMTime());
            ++$count;
        }

        return \sprintf('%d-%d', $mtime, $count);
    }

    /**
     * @return iterable<SplFileInfo>
     */
    private function files(string $locale): iterable
    {
        $directory = \sprintf('%s/%s', $this->blogContentDir, $locale);
        if (!is_dir($directory)) {
            return [];
        }

        return new Finder()
            ->in($directory)
            ->files()
            ->name(self::FILE_PATTERN)
        ;
    }

    private function read(string $path): string
    {
        $raw = is_file($path) ? file_get_contents($path) : false;
        if (false === $raw) {
            throw UnreadableBlogPostException::atPath($path);
        }

        return $raw;
    }
}
