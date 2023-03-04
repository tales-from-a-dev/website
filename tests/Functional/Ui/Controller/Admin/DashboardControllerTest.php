<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Admin;

use App\Core\Enum\Role;
use App\Tests\Factory\PostFactory;
use App\Tests\Factory\ProjectFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

final class DashboardControllerTest extends WebTestCase
{
    use Factories;

    public function testItCanViewAdminHomePage(): void
    {
        PostFactory::createMany(5);
        ProjectFactory::createMany(5);

        self::ensureKernelShutdown();

        $client = static::createClient();
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $client->loginUser(new InMemoryUser(
            username: 'user@example.com',
            password: '$2y$13$.HTrY6My5GMKXPtBaAo4yuYxi3w2VvstIOWveXCwjbTusEGc6NR8m',
            roles: [Role::User->value]
        ));
        $crawler = $client->request(Request::METHOD_GET, '/admin');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $translator->trans('dashboard.title'), $translator->trans('app.meta.title')));

        // posts
        self::assertSelectorTextContains('div[id=latest-posts] h3', $translator->trans('dashboard.latest_posts'));
        self::assertCount(5, $crawler->filter('div[id=latest-posts] table > tbody > tr'));

        // projects
        self::assertSelectorTextContains('div[id=latest-projects] h3', $translator->trans('dashboard.latest_projects'));
        self::assertCount(5, $crawler->filter('div[id=latest-projects] table > tbody > tr'));
    }
}
