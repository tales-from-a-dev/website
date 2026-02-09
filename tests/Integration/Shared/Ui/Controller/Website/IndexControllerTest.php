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
            ->assertSeeIn('h6', $translator->trans('website.index.section.about.surtitle'))
            ->assertSeeIn('h1', $translator->trans('website.index.section.about.title'))
            ->assertElementCount('section', 4)
        ;
    }
}
