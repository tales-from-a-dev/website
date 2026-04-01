<?php

declare(strict_types=1);

namespace App\Tests\Integration\Resume\Ui\Controller\Website;

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
            ->visit('/cv')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('website.resume.title'),
                $translator->trans('app.meta.title')
            ))
            ->assertSeeIn('h1', $translator->trans('website.resume.name'))
            ->assertElementCount('section', 7)
        ;
    }
}
