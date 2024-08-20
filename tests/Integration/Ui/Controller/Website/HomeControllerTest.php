<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class HomeControllerTest extends WebTestCase
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
                $translator->trans('home.title'),
                $translator->trans('app.meta.title')
            ))
            ->assertElementAttributeContains(
                'head meta[name=description]',
                'content',
                $translator->trans('app.meta.description')
            )
            ->assertSeeIn('h1', \sprintf(
                '< %s />',
                $translator->trans('app.meta.title')
            ))
            ->assertElementCount('a', 4)
        ;
    }
}
