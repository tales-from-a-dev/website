<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
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
        self::assertPageTitleSame(sprintf('%s | %s', $this->translator->trans('admin.home.title'), $this->translator->trans('app.meta.title')));
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
        self::assertSelectorTextContains('div[role=alert] > div', $this->translator->trans(id: 'Invalid credentials.', domain: 'security'));
    }
}
