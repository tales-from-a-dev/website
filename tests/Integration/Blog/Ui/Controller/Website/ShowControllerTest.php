<?php

declare(strict_types=1);

namespace App\Tests\Integration\Blog\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class ShowControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItLinksTheCategoryToItsArchive(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/blog/first-post')
            ->assertSuccessful()
            ->assertSeeIn(
                'article header a[href="/blog/category/architecture"]',
                $translator->trans('enum.blog_category.architecture'),
            )
        ;
    }

    public function testItCanViewBlogPostPage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/blog/first-post')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                'First post',
                $translator->trans('app.meta.title')
            ))
            ->assertElementAttributeContains(
                'head meta[name=description]',
                'content',
                'The oldest published fixture.'
            )
            ->assertSeeIn('h1', 'First post')
            ->assertSeeIn('[data-slot=post-content]', 'The oldest published post')
            ->assertElementCount('[data-slot=post-content] pre', 1)
            ->assertSee($translator->trans('website.blog.back_to_index'))
        ;
    }

    public function testItCanViewFrenchBlogPostPage(): void
    {
        $this->browser()
            ->visit('/fr/blog/premier-article')
            ->assertSuccessful()
            ->assertSeeIn('h1', 'Premier article')
        ;
    }

    public function testItPointsHreflangAtTheTranslatedSlug(): void
    {
        $this->browser()
            ->visit('/blog/first-post')
            ->assertSuccessful()
            ->assertElementAttributeContains(
                'head link[rel=alternate][hreflang=en]',
                'href',
                'https://localhost/blog/first-post'
            )
            ->assertElementAttributeContains(
                'head link[rel=alternate][hreflang=fr]',
                'href',
                'https://localhost/fr/blog/premier-article'
            )
            ->assertElementAttributeContains(
                'head link[rel=alternate][hreflang=x-default]',
                'href',
                'https://localhost/blog/first-post'
            )
        ;
    }

    public function testItSelfReferencesHreflangForAnUntranslatedPost(): void
    {
        $this->browser()
            ->visit('/blog/untranslated-post')
            ->assertSuccessful()
            ->assertElementCount('head link[rel=alternate][hreflang=en]', 1)
            ->assertElementCount('head link[rel=alternate][hreflang=fr]', 0)
            ->assertElementAttributeContains(
                'head link[rel=alternate][hreflang=x-default]',
                'href',
                'https://localhost/blog/untranslated-post'
            )
        ;
    }

    public function testItSwitchesLocaleToTheTranslatedSlug(): void
    {
        $this->browser()
            ->visit('/blog/first-post')
            ->assertSuccessful()
            ->assertElementAttributeContains(
                '[data-slot=locale-switcher] a[hreflang=fr]',
                'href',
                '/fr/blog/premier-article'
            )
        ;
    }

    public function testItOffersNoLocaleSwitchForAnUntranslatedPost(): void
    {
        $this->browser()
            ->visit('/blog/untranslated-post')
            ->assertSuccessful()
            ->assertElementCount('[data-slot=locale-switcher] a[hreflang=fr]', 0)
            ->assertElementCount('[data-slot=locale-switcher] [aria-disabled=true]', 1)
        ;
    }

    public function testItEmitsBlogPostingStructuredData(): void
    {
        $this->browser()
            ->visit('/blog/first-post')
            ->assertSuccessful()
            ->assertSeeIn('script[type="application/ld+json"]', '"@type":"BlogPosting"')
            ->assertSeeIn('script[type="application/ld+json"]', '"headline":"First post"')
            ->assertSeeIn('script[type="application/ld+json"]', '"datePublished":"2026-01-15"')
            ->assertSeeIn('script[type="application/ld+json"]', '"inLanguage":"en"')
        ;
    }

    public function testItEmitsOpenGraphImageOnlyWhenThePostHasACover(): void
    {
        $this->browser()
            ->visit('/blog/untranslated-post')
            ->assertSuccessful()
            ->assertElementCount('head meta[property="og:image"]', 1)
        ;

        $this->browser()
            ->visit('/blog/first-post')
            ->assertSuccessful()
            ->assertElementCount('head meta[property="og:image"]', 0)
        ;
    }

    public function testItReturnsNotFoundForADraftPost(): void
    {
        $this->browser()
            ->visit('/blog/draft-post')
            ->assertStatus(Response::HTTP_NOT_FOUND)
        ;
    }

    public function testItReturnsNotFoundForAnUnknownSlug(): void
    {
        $this->browser()
            ->visit('/blog/there-is-no-such-post')
            ->assertStatus(Response::HTTP_NOT_FOUND)
        ;
    }

    public function testItReturnsNotFoundForAPostFromAnotherLocale(): void
    {
        $this->browser()
            ->visit('/fr/blog/untranslated-post')
            ->assertStatus(Response::HTTP_NOT_FOUND)
        ;
    }
}
