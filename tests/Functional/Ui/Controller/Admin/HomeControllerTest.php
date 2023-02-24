<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Admin;

use App\Core\Enum\Role;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
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

    public function testItCanViewAdminHomePage(): void
    {
        $this->client->loginUser(new InMemoryUser(
            username: 'user@example.com',
            password: '$2y$13$.HTrY6My5GMKXPtBaAo4yuYxi3w2VvstIOWveXCwjbTusEGc6NR8m',
            roles: [Role::User->value]
        ));
        $this->client->request(Request::METHOD_GET, '/admin');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans('admin.home.title'), $this->translator->trans('app.meta.title')));
    }
}
