<?php

declare(strict_types=1);

namespace App\Tests\Integration\Shared\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class IndexControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewHomePage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('website.index.title'),
                $translator->trans('app.meta.title')
            ))
            ->assertElementAttributeContains(
                'head meta[name=description]',
                'content',
                $translator->trans('app.meta.description')
            )
            ->assertElementCount('section', 6)
            ->assertSeeIn('#hero h1', $translator->trans('app.author'))
            ->assertSeeIn('#hero p', $translator->trans('app.job_title'))
        ;
    }

    public function testItShowsTheLatestPostsBeforeTheOpenSourceSection(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $sections = $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertSeeIn('#blog [data-slot=section-title]', $translator->trans('website.index.section.blog.title'))
            ->assertSeeElement('#blog a[data-slot=button][href="/blog"]')
            ->crawler()
            ->filter('main section')
            ->each(static fn ($node): string => (string) $node->attr('id'))
        ;

        self::assertSame(['hero', 'blog', 'open-source-projects', 'experience', 'contact', 'faq'], $sections);
    }

    public function testItListsTheLatestPostsNewestFirst(): void
    {
        $titles = $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertElementCount('#blog [data-slot=post]', 3)
            ->crawler()
            ->filter('#blog [data-slot=post-title]')
            ->each(static fn ($node): string => trim($node->text()))
        ;

        self::assertSame(['Untranslated post', 'Second post', 'First post'], $titles);
    }

    public function testEachPostCardShowsItsTitleCategoryTagsAndDate(): void
    {
        $card = $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->crawler()
            ->filter('#blog [data-slot=post]')
            ->last()
        ;

        self::assertSame('First post', trim($card->filter('h3[data-slot=post-title]')->text()));
        self::assertSame('2026-01-15', $card->filter('time')->attr('datetime'));
        self::assertSame(
            ['Architecture', 'php', 'symfony'],
            $card->filter('[data-slot=badge]')->each(static fn ($node): string => trim($node->text())),
        );
    }

    public function testItShowsThePostsOfTheRequestedLocale(): void
    {
        // without the header, RedirectToPreferredLocale sends /fr back to the English homepage
        $this->browser()
            ->request('GET', '/fr', ['headers' => ['Accept-Language' => 'fr']])
            ->assertSuccessful()
            ->assertSee('Premier article')
            ->assertNotSee('Untranslated post')
            ->assertElementCount('#blog [data-slot=post]', 2)
            ->assertSeeElement('#blog a[data-slot=button][href="/fr/blog"]')
        ;
    }

    public function testItSwitchesLocaleOnARouteWithoutParameters(): void
    {
        $this->browser()
            ->visit('/')
            ->assertSuccessful()
            ->assertElementAttributeContains('[data-role=locale-switcher] a[hreflang=fr]', 'href', '/fr')
        ;
    }
}
