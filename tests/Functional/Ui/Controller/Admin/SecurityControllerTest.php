<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Admin;

use App\Core\Enum\Role;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

    public function testItCanViewAdminLoginPage(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/admin/login');

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans('login.title'), $this->translator->trans('app.meta.title')));
        self::assertCount(1, $crawler->filter('form'));
    }

    public function testItCanLogin(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/login');
        $this->client->submitForm(
            $this->translator->trans(id: 'btn.login', domain: 'form'),
            [
                '_email' => 'user@example.com',
                '_password' => 'password',
            ]
        );
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans(id: 'dashboard.title', domain: 'admin'), $this->translator->trans('app.meta.title')));
    }

    public function testItRedirectToAdminDashboardIfAlreadyLogin(): void
    {
        $this->client->loginUser(new InMemoryUser(
            username: $this->getContainer()->getParameter('user.email'),
            password: $this->getContainer()->getParameter('user.password'),
            roles: [Role::User->value]
        ));
        $this->client->request(Request::METHOD_GET, '/admin/login');
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans(id: 'dashboard.title', domain: 'admin'), $this->translator->trans('app.meta.title')));
    }

    public function testItTriggerAnErrorWithInvalidCredentials(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/login');
        $this->client->submitForm(
            $this->translator->trans(id: 'btn.login', domain: 'form'),
            [
                '_email' => 'user',
                '_password' => 'drowssap',
            ]
        );
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'Invalid credentials.', domain: 'security'));
    }

    public function testItTriggerAnErrorWithTooManyAttempts(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/login');
        $this->client->submitForm(
            $this->translator->trans(id: 'btn.login', domain: 'form'),
            [
                '_email' => 'user',
                '_password' => 'drowssap',
            ]
        );
        $this->client->followRedirect();
        $this->client->submitForm(
            $this->translator->trans(id: 'btn.login', domain: 'form'),
            [
                '_email' => 'user',
                '_password' => 'drowssap',
            ]
        );
        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert] > p', $this->translator->trans(id: 'Too many failed login attempts, please try again in %minutes% minute.', parameters: ['%minutes%' => 1], domain: 'security'));
    }
}
