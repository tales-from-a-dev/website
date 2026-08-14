<?php

declare(strict_types=1);

namespace App\Tests\Integration\Blog\Infrastructure\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Browser\Test\HasBrowser;

final class BlogSitemapListenerTest extends WebTestCase
{
    use HasBrowser;

    public function testItListsEveryPublishedPostOfEveryLocale(): void
    {
        $content = $this->sitemap();

        self::assertStringContainsString('<loc>https://localhost/blog/first-post</loc>', $content);
        self::assertStringContainsString('<loc>https://localhost/blog/second-post</loc>', $content);
        self::assertStringContainsString('<loc>https://localhost/blog/untranslated-post</loc>', $content);
        self::assertStringContainsString('<loc>https://localhost/fr/blog/premier-article</loc>', $content);
        self::assertStringContainsString('<loc>https://localhost/fr/blog/deuxieme-article</loc>', $content);
    }

    public function testItPointsAlternatesAtTheTranslatedSlug(): void
    {
        $content = $this->sitemap();

        self::assertStringContainsString(
            '<xhtml:link rel="alternate" hreflang="en" href="https://localhost/blog/first-post" />',
            $content,
        );
        self::assertStringContainsString(
            '<xhtml:link rel="alternate" hreflang="fr" href="https://localhost/fr/blog/premier-article" />',
            $content,
        );
    }

    public function testItSelfReferencesAnUntranslatedPost(): void
    {
        self::assertStringNotContainsString('https://localhost/fr/blog/untranslated-post', $this->sitemap());
    }

    public function testItExcludesDraftsAndTagArchives(): void
    {
        $content = $this->sitemap();

        self::assertStringNotContainsString('draft-post', $content);
        self::assertStringNotContainsString('/blog/tag/', $content);
    }

    public function testItKeepsTheIndexAlongsideThePosts(): void
    {
        self::assertStringContainsString('<loc>https://localhost/blog</loc>', $this->sitemap());
    }

    private function sitemap(): string
    {
        return $this->browser()
            ->visit('/sitemap.blog.xml')
            ->assertSuccessful()
            ->content()
        ;
    }
}
