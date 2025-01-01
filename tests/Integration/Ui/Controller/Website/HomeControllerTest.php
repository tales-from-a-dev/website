<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use App\Domain\Dto\ContactDto;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class HomeControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewHomePage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/')

            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('home.title'),
                $translator->trans('app.meta.title')
            ))
            ->assertElementAttributeContains(
                'head meta[name=description]',
                'content',
                $translator->trans('app.meta.description')
            )
            ->assertSeeIn('h6', $translator->trans('home.section.about.surtitle'))
            ->assertSeeIn('h1', $translator->trans('home.section.about.title'))
            ->assertElementCount('section', 4)
        ;
    }

    public function testItCanSendAMessage(): void
    {
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $contact = new ContactDto(
            fullName: 'John Doe',
            company: 'ACME Corp',
            email: 'johndoe@example.com',
            content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lectus nibh, tristique sed lobortis ut, facilisis sed neque. Pellentesque quis mauris volutpat, vehicula mi sed, posuere velit.'
        );

        $this->browser()
            ->visit('/')

            ->fillField('contact_fullName', $contact->fullName)
            ->fillField('contact_company', $contact->company)
            ->fillField('contact_email', $contact->email)
            ->fillField('contact_content', $contact->content)
            ->click('submit')

            ->assertSuccessful()
            ->assertOn('/')
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
            ->visit('/')

            ->fillField('contact_fullName', '')
            ->fillField('contact_email', '')
            ->fillField('contact_content', '')
            ->click('submit')

            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertSeeIn(
                'p[id=contact_fullName_error_0]',
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
