<?php

declare(strict_types=1);

namespace App\Tests\Functional\Http\Controller\Website;

use App\Domain\Project\Factory\ProjectFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;

final class ProjectControllerTest extends WebTestCase
{
    use Factories;

    /**
     * @dataProvider getIndexUri
     */
    public function testItCanViewIndexPage(string $uri): void
    {
        ProjectFactory::new()->asCustomerProject()->many(10)->create();

        self::ensureKernelShutdown();

        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, $uri);

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

        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, $uri);

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('article'));
        self::assertSelectorTextContains('h1', $project->getTitle());
    }

    public function getIndexUri(): \Generator
    {
        yield ['/projets'];
        yield ['/en/projects'];
    }

    public function getShowUri(): \Generator
    {
        yield ['/projets/dummy-project'];
        yield ['/en/projects/dummy-project'];
    }
}
