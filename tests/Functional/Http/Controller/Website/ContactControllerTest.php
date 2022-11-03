<?php

declare(strict_types=1);

namespace App\Tests\Functional\Http\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContactControllerTest extends WebTestCase
{
    use MailerAssertionsTrait;

    public function testItCanViewIndexPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/contact');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('form'));
    }

    public function testItCanSendAMessage(): void
    {
        $client = static::createClient();
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $client->request(Request::METHOD_GET, '/contact');
        $client->submitForm('submit', [
            'contact[name]' => 'John Doe',
            'contact[email]' => 'johndoe@example.com',
            'contact[content]' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lectus nibh, tristique sed lobortis ut, facilisis sed neque. Pellentesque quis mauris volutpat, vehicula mi sed, posuere velit.',
        ]);

        $email = self::getMailerMessage();

        self::assertEmailCount(1);
        self::assertEmailHeaderSame($email, 'From', 'John Doe <johndoe@example.com>');
        self::assertEmailAddressContains($email, 'From', 'johndoe@example.com');

        $crawler = $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('.alert'));
        self::assertSelectorTextContains('.alert-success', $translator->trans(id: 'contact.send.success', domain: 'alert'));
    }

    public function testItTriggerErrorsWithInvalidData(): void
    {
        $client = static::createClient();
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $client->request(Request::METHOD_GET, '/contact');
        $client->followRedirects();
        $crawler = $client->submitForm('submit', [
            'contact[name]' => '',
            'contact[email]' => '',
            'contact[content]' => '',
        ]);

        self::assertResponseIsUnprocessable();
        self::assertCount(3, $crawler->filter('ul > li'));
        self::assertSelectorTextContains('ul > li', $translator->trans(id: 'This value should not be blank.', domain: 'validators'));
    }
}
