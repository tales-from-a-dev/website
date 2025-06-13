<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class ContactControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewHomePage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/contact')
            ->assertSuccessful()
            ->assertSeeIn('[data-slot=card-description]', $translator->trans('home.section.contact.text'))
        ;
    }

    public function testItCanSendAMessage(): void
    {
        $this->browser()
            ->visit('/contact')
            ->fillField('contact_fullName', 'John Doe')
            ->fillField('contact_company', 'ACME Corp')
            ->fillField('contact_email', 'johndoe@example.com')
            ->fillField('contact_content', 'Hello World')
            ->click('contact_submit')
            ->assertSuccessful()
        ;
    }

    public function testItTriggerErrorsWithInvalidData(): void
    {
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/contact')
            ->fillField('contact_fullName', '')
            ->fillField('contact_email', 'johndoe@example')
            ->fillField('contact_content', '')
            ->click('contact_submit')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSeeIn(
                'p[id=contact_fullName_error_0]',
                $translator->trans(id: 'This value should not be blank.', domain: 'validators')
            )
            ->assertSeeIn(
                'p[id=contact_email_error_0]',
                $translator->trans(id: 'This value is not a valid email address.', domain: 'validators')
            )
            ->assertSeeIn(
                'p[id=contact_content_error_0]',
                $translator->trans(id: 'This value should not be blank.', domain: 'validators')
            )
        ;
    }
}
