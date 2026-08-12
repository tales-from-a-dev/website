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
