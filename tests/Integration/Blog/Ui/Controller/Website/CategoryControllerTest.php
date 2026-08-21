<?php

declare(strict_types=1);

namespace App\Tests\Integration\Blog\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class CategoryControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewBlogCategoryPage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);
        $label = $translator->trans('enum.blog_category.architecture');

        $this->browser()
            ->visit('/blog/category/architecture')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('website.blog.category.title', ['category' => $label]),
                $translator->trans('app.meta.title')
            ))
            ->assertSeeIn('h1', $translator->trans('website.blog.category.title', ['category' => $label]))
            ->assertSee($translator->trans('website.blog.back_to_index'))
            ->assertElementCount('[data-slot=card]', 1)
        ;
    }

    public function testItListsOnlyThePostsOfTheCategory(): void
    {
        $this->browser()
            ->visit('/blog/category/performance')
            ->assertSuccessful()
            ->assertSee('Untranslated post')
            ->assertNotSee('First post')
            ->assertNotSee('Second post')
            ->assertElementCount('[data-slot=card]', 1)
        ;
    }

    public function testItListsOnlyFrenchPostsOnTheFrenchArchive(): void
    {
        $this->browser()
            ->visit('/fr/blog/category/architecture')
            ->assertSuccessful()
            ->assertSee('Premier article')
            ->assertNotSee('Deuxième article')
            ->assertNotSee('First post')
            ->assertElementCount('[data-slot=card]', 1)
        ;
    }

    public function testItTranslatesTheCategoryLabel(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/fr/blog/category/architecture')
            ->assertSuccessful()
            ->assertSeeIn('h1', $translator->trans('enum.blog_category.architecture', locale: 'fr'))
        ;
    }

    public function testItLetsTheArchiveBeIndexed(): void
    {
        $this->browser()
            ->visit('/blog/category/architecture')
            ->assertSuccessful()
            ->assertElementAttributeContains('head meta[name=robots]', 'content', 'index, follow')
        ;
    }

    public function testItAdvertisesAnAlternateForEveryLocaleHoldingTheCategory(): void
    {
        $this->browser()
            ->visit('/blog/category/architecture')
            ->assertSuccessful()
            ->assertElementAttributeContains('head link[hreflang=en]', 'href', 'https://localhost/blog/category/architecture')
            ->assertElementAttributeContains('head link[hreflang=fr]', 'href', 'https://localhost/fr/blog/category/architecture')
            ->assertElementAttributeContains('head link[hreflang=x-default]', 'href', 'https://localhost/blog/category/architecture')
        ;
    }

    public function testItSelfReferencesACategoryHeldByASingleLocale(): void
    {
        $this->browser()
            ->visit('/blog/category/performance')
            ->assertSuccessful()
            ->assertElementAttributeContains('head link[hreflang=en]', 'href', 'https://localhost/blog/category/performance')
            ->assertNotSeeElement('head link[hreflang=fr]')
        ;
    }

    public function testItDisablesTheLocaleSwitcherWithoutACounterpart(): void
    {
        $this->browser()
            ->visit('/blog/category/performance')
            ->assertSuccessful()
            ->assertSeeElement('[data-slot=locale-switcher] span[aria-disabled=true]')
        ;
    }

    public function testItReturnsNotFoundForACategoryOnlyHeldByADraft(): void
    {
        $this->browser()
            ->visit('/blog/category/testing')
            ->assertStatus(Response::HTTP_NOT_FOUND)
        ;
    }

    public function testItReturnsNotFoundForAnUnknownCategory(): void
    {
        $this->browser()
            ->visit('/blog/category/there-is-no-such-category')
            ->assertStatus(Response::HTTP_NOT_FOUND)
        ;
    }
}
