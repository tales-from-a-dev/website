<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Admin;

use App\Core\Enum\Role;
use App\Tests\Factory\PostFactory;
use App\Tests\Factory\ProjectFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

final class DashboardControllerTest extends WebTestCase
{
    use Factories;

    private KernelBrowser $client;
    private TranslatorInterface $translator;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->loginUser(new InMemoryUser(
            username: self::getContainer()->getParameter('user.email'),
            password: self::getContainer()->getParameter('user.password'),
            roles: [Role::User->value]
        ));
        $this->translator = self::getContainer()->get(TranslatorInterface::class);
    }

    public function testItCanViewAdminHomePage(): void
    {
        PostFactory::createMany(5);
        ProjectFactory::createMany(5);

        self::ensureKernelShutdown();

        $crawler = $this->client->request(Request::METHOD_GET, '/admin');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(\sprintf('%s | %s', $this->translator->trans(id: 'dashboard.title', domain: 'admin'), $this->translator->trans('app.meta.title')));

        // posts
        self::assertSelectorTextContains('div[id=latest-posts] h3', $this->translator->trans(id: 'dashboard.latest_posts', domain: 'admin'));
        self::assertCount(5, $crawler->filter('div[id=latest-posts] table > tbody > tr'));

        // projects
        self::assertSelectorTextContains('div[id=latest-projects] h3', $this->translator->trans(id: 'dashboard.latest_projects', domain: 'admin'));
        self::assertCount(5, $crawler->filter('div[id=latest-projects] table > tbody > tr'));
    }
}
