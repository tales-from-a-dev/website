<?php

declare(strict_types=1);

namespace App\Tests\Integration\Resume\Ui\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class IndexControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewHomePage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/cv')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('website.resume.title'),
                $translator->trans('app.meta.title')
            ))
            ->assertSeeIn('h1', $translator->trans('app.author'))
            ->assertElementCount('section', 1)
        ;
    }

    public function testItRendersContactDetails(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/cv')
            ->assertSuccessful()
            ->assertSee($translator->trans('website.resume.contact.location'))
            ->assertSeeElement('a[href="mailto:monteil.romain@gmail.com"]')
            ->assertSeeElement('a[href="https://talesfroma.dev"]')
            ->assertSeeElement('a[href="https://github.com/ker0x"]')
            ->assertSeeElement('a[href="https://www.linkedin.com/in/romain-monteil/"]')
            ->assertSeeElement('a[href="https://connect.symfony.com/profile/ker0x"]')
        ;
    }

    public function testItRendersEveryResumeSection(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $browser = $this->browser()
            ->visit('/cv')
            ->assertSuccessful()
        ;

        foreach (['skills', 'certifications', 'languages', 'other', 'experience', 'education'] as $section) {
            $browser->assertSee($translator->trans(\sprintf('website.resume.section.%s.title', $section)));
        }
    }

    public function testItRendersTheOtherSectionItems(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/cv')
            ->assertSuccessful()
            ->assertSee($translator->trans('website.resume.section.other.item.afup'))
            ->assertSee($translator->trans('website.resume.section.other.item.oss'))
        ;
    }
}
