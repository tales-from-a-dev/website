<?php

declare(strict_types=1);

namespace App\Tests\Integration\Blog\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class TagControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewBlogTagPage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/blog/tag/symfony')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('website.blog.tag.title', ['tag' => 'symfony']),
                $translator->trans('app.meta.title')
            ))
            ->assertSeeIn('h1', $translator->trans('website.blog.tag.title', ['tag' => 'symfony']))
            ->assertSee($translator->trans('website.blog.back_to_index'))
            ->assertElementCount('[data-slot=card]', 2)
        ;
    }

    public function testItListsOnlyThePostsCarryingTheTag(): void
    {
        $this->browser()
            ->visit('/blog/tag/php')
            ->assertSuccessful()
            ->assertSee('First post')
            ->assertSee('Untranslated post')
            ->assertNotSee('Second post')
            ->assertElementCount('[data-slot=card]', 2)
        ;
    }

    public function testItListsOnlyFrenchPostsOnTheFrenchArchive(): void
    {
        $this->browser()
            ->visit('/fr/blog/tag/symfony')
            ->assertSuccessful()
            ->assertSee('Premier article')
            ->assertSee('Deuxième article')
            ->assertNotSee('Untranslated post')
            ->assertElementCount('[data-slot=card]', 2)
        ;
    }

    public function testItKeepsTheArchiveOutOfTheIndex(): void
    {
        $this->browser()
            ->visit('/blog/tag/symfony')
            ->assertSuccessful()
            ->assertElementAttributeContains('head meta[name=robots]', 'content', 'noindex, follow')
        ;
    }

    public function testItReturnsNotFoundForATagOnlyCarriedByADraft(): void
    {
        $this->browser()
            ->visit('/blog/tag/draft-only')
            ->assertStatus(Response::HTTP_NOT_FOUND)
        ;
    }

    public function testItReturnsNotFoundForAnUnknownTag(): void
    {
        $this->browser()
            ->visit('/blog/tag/there-is-no-such-tag')
            ->assertStatus(Response::HTTP_NOT_FOUND)
        ;
    }
}
