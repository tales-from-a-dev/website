<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use App\Tests\Factory\ProjectFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;

final class ProjectControllerTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    #[DataProvider('getIndexUri')]
    public function testItCanViewIndexPage(string $uri): void
    {
        ProjectFactory::new()->asCustomerProject()->many(10)->create();

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(5, $crawler->filter('article'));
    }

    #[DataProvider('getShowUri')]
    public function testItCanViewShowPage(string $uri): void
    {
        $project = ProjectFactory::createOne(['title' => 'Dummy Project']);

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('article'));
        self::assertSelectorTextContains('h1', $project->getTitle());
    }

    public static function getIndexUri(): iterable
    {
        yield ['/projets'];
    }

    public static function getShowUri(): iterable
    {
        yield ['/projets/dummy-project'];
    }
}
