<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HomeControllerTest extends WebTestCase
{
    public function testItCanViewHomePage(): void
    {
        $client = static::createClient();
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $crawler = $client->request(Request::METHOD_GET, '/');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $translator->trans('home.title'), $translator->trans('app.meta.title')));
        self::assertSame($translator->trans('app.meta.description'), $crawler->filterXPath('//meta[@name="description"]')->extract(['content'])[0]);
        self::assertSame(sprintf('< %s />', $translator->trans('app.meta.title')), $crawler->filter('h1')->text());
        self::assertCount(4, $crawler->filter('a'));
    }
}
