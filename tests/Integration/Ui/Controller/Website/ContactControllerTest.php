<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use App\Domain\Contact\Model\Contact;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\NotificationAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class ContactControllerTest extends WebTestCase
{
    use MailerAssertionsTrait;
    use NotificationAssertionsTrait;

    private KernelBrowser $client;
    private TranslatorInterface $translator;
    private Environment $twig;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->translator = self::getContainer()->get(TranslatorInterface::class);
        $this->twig = self::getContainer()->get(Environment::class);
    }

    public function testItCanViewIndexPage(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/contact');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('form'));
    }

    public function testItCanSendAMessage(): void
    {
        $contact = new Contact(
            name: 'John Doe',
            email: 'johndoe@example.com',
            content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lectus nibh, tristique sed lobortis ut, facilisis sed neque. Pellentesque quis mauris volutpat, vehicula mi sed, posuere velit.'
        );

        $this->client->request(Request::METHOD_GET, '/contact');
        $this->client->submitForm('submit', [
            'contact[name]' => $contact->name,
            'contact[email]' => $contact->email,
            'contact[content]' => $contact->content,
        ]);

        $notification = self::getNotifierMessage();

        self::assertNotificationCount(1);
        self::assertNotificationSubjectContains(
            $notification,
            $this->translator->trans(id: 'contact.subject', parameters: ['name' => $contact->name], domain: 'notification')
        );

        $email = self::getMailerMessage();

        self::assertEmailCount(1);
        self::assertEmailHeaderSame($email, 'From', 'Tales from a Dev <noreply@talesfroma.dev>');
        self::assertEmailAddressContains($email, 'From', 'noreply@talesfroma.dev');
        self::assertEmailHeaderSame($email, 'To', 'Contact <test@example.com>');
        self::assertEmailAddressContains($email, 'To', 'test@example.com');
        self::assertEmailHtmlBodyContains($email, $this->twig->render('email/contact.html.twig', ['contact' => $contact]));

        $crawler = $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('div[role=alert]'));
        self::assertSelectorTextContains('div[role=alert]', $this->translator->trans(id: 'contact.send.success', domain: 'alert'));
    }

    public function testItTriggerErrorsWithInvalidData(): void
    {
        $this->client->request(Request::METHOD_GET, '/contact');
        $this->client->followRedirects();
        $crawler = $this->client->submitForm('submit', [
            'contact[name]' => '',
            'contact[email]' => '',
            'contact[content]' => '',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(3, $crawler->filter('p.mt-2.text-sm.text-red-600.dark\:text-red-500'));
        self::assertSelectorTextContains('p.mt-2.text-sm.text-red-600.dark\:text-red-500', $this->translator->trans(id: 'This value should not be blank.', domain: 'validators'));
    }
}
