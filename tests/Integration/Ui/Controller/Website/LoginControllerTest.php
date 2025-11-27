<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use App\Test\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;

final class LoginControllerTest extends WebTestCase
{
    use Factories;
    use HasBrowser;

    public function testItCanViewLoginPage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/login')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('login.title'),
                $translator->trans('app.meta.title')
            ))
        ;
    }

    public function testItCanLogin(): void
    {
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $this->browser()
            ->visit('/login')
            ->fillField('_email', $user->email)
            ->fillField('_password', 'password')
            ->click('submit')
            ->assertSuccessful()
            ->assertOn('/settings')
        ;
    }

    public function testItTriggerErrorsWithInvalidCredentials(): void
    {
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/login')
            ->fillField('_email', 'user@example.com')
            ->fillField('_password', 'drowssap')
            ->click('submit')
            ->assertSuccessful()
            ->assertOn('/login')
            ->assertSeeIn(
                'h3[data-slot=alert-title]',
                $translator->trans(id: 'Invalid credentials.', domain: 'security')
            )
        ;
    }
}
