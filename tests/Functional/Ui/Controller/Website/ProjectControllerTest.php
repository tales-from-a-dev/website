<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Website;

use App\Tests\Factory\ProjectFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;

final class ProjectControllerTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider getIndexUri
     */
    public function testItCanViewIndexPage(string $uri): void
    {
        ProjectFactory::new()->asCustomerProject()->many(10)->create();

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(5, $crawler->filter('article'));
    }

    /**
     * @dataProvider getShowUri
     */
    public function testItCanViewShowPage(string $uri): void
    {
        $project = ProjectFactory::createOne(['title' => 'Dummy Project']);

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('article'));
        self::assertSelectorTextContains('h1', $project->getTitle());
    }

    public static function getIndexUri(): \Generator
    {
        yield ['/projets'];
        yield ['/en/projects'];
    }

    public static function getShowUri(): \Generator
    {
        yield ['/projets/dummy-project'];
        yield ['/en/projects/dummy-project'];
    }
}
