<?php

declare(strict_types=1);

namespace App\Tests\Functional\Http\Controller\Website;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

    public function testItCanViewHomePage(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans('home.title'), $this->translator->trans('app.meta.title')));
        self::assertSame($this->translator->trans('app.meta.description'), $crawler->filterXPath('//meta[@name="description"]')->extract(['content'])[0]);
        self::assertSame(sprintf('< %s />', $this->translator->trans('app.meta.title')), $crawler->filter('h1')->text());
        self::assertCount(3, $crawler->filter('a'));
    }
}
