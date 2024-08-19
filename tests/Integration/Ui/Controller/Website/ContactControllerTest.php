<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use App\Domain\Model\Contact;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class ContactControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewIndexPage(): void
    {
        $this->browser()
            ->visit('/contact')

            ->assertSuccessful()
            ->assertElementCount('form', 1)
        ;
    }

    public function testItCanSendAMessage(): void
    {
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $contact = new Contact(
            name: 'John Doe',
            email: 'johndoe@example.com',
            content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lectus nibh, tristique sed lobortis ut, facilisis sed neque. Pellentesque quis mauris volutpat, vehicula mi sed, posuere velit.'
        );

        $this->browser()
            ->visit('/contact')

            ->fillField('contact_name', $contact->name)
            ->fillField('contact_email', $contact->email)
            ->fillField('contact_content', $contact->content)
            ->click('submit')

            ->assertSuccessful()
            ->assertOn('/contact')
            ->assertElementCount('div[role=alert]', 1)
            ->assertSeeIn(
                'div[role=alert]',
                $translator->trans(id: 'contact.send.success', domain: 'alert')
            )
        ;
    }

    public function testItTriggerErrorsWithInvalidData(): void
    {
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/contact')

            ->fillField('contact_name', '')
            ->fillField('contact_email', '')
            ->fillField('contact_content', '')
            ->click('submit')

            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSeeIn(
                'p[id=contact_name_error_0]',
                $translator->trans(id: 'This value should not be blank.', domain: 'validators')
            )
            ->assertSeeIn(
                'p[id=contact_email_error_0]',
                $translator->trans(id: 'This value should not be blank.', domain: 'validators')
            )
            ->assertSeeIn(
                'p[id=contact_content_error_0]',
                $translator->trans(id: 'This value should not be blank.', domain: 'validators')
            )
        ;
    }
}
