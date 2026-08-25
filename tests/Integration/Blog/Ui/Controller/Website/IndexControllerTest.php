<?php

declare(strict_types=1);

namespace App\Tests\Integration\Blog\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class IndexControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewBlogIndexPage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/blog')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('website.blog.title'),
                $translator->trans('app.meta.title')
            ))
            ->assertElementAttributeContains(
                'head meta[name=description]',
                'content',
                $translator->trans('website.blog.meta.description')
            )
            ->assertSeeIn('h1', $translator->trans('website.blog.title'))
            ->assertElementCount('[data-slot=post]', 3)
            ->assertElementCount('[data-slot=post] [data-slot=reading-time]', 3)
            ->assertSeeIn(
                '[data-slot=post] [data-slot=reading-time]',
                $translator->trans('website.blog.reading_time', ['minutes' => 1])
            )
        ;
    }

    public function testItListsEnglishPostsNewestFirst(): void
    {
        $titles = $this->browser()
            ->visit('/blog')
            ->assertSuccessful()
            ->crawler()
            ->filter('[data-slot=post-title]')
            ->each(static fn ($node): string => trim($node->text()))
        ;

        self::assertSame(['Untranslated post', 'Second post', 'First post'], $titles);
    }

    public function testItListsOnlyFrenchPostsOnTheFrenchIndex(): void
    {
        $this->browser()
            ->visit('/fr/blog')
            ->assertSuccessful()
            ->assertSee('Premier article')
            ->assertSee('Deuxième article')
            ->assertNotSee('Untranslated post')
            ->assertElementCount('[data-slot=post]', 2)
        ;
    }

    public function testItHidesDraftsFromTheIndex(): void
    {
        $this->browser()
            ->visit('/blog')
            ->assertSuccessful()
            ->assertNotSee('Draft post')
        ;
    }
}
