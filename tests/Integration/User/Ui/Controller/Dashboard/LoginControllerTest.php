<?php

declare(strict_types=1);

namespace App\Tests\Integration\User\Ui\Controller\Dashboard;

use App\User\Test\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class LoginControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewLoginPage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/dashboard/login')
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('dashboard.user.login.title.page'),
                $translator->trans('app.meta.title')
            ))
        ;
    }

    public function testItCanLogin(): void
    {
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $this->browser()
            ->visit('/dashboard/login')
            ->fillField('_email', $user->email)
            ->fillField('_password', 'password')
            ->click('submit')
            ->assertSuccessful()
            ->assertOn('/dashboard')
        ;
    }

    public function testItTriggerErrorsWithInvalidCredentials(): void
    {
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->visit('/dashboard/login')
            ->fillField('_email', 'user@example.com')
            ->fillField('_password', 'drowssap')
            ->click('submit')
            ->assertSuccessful()
            ->assertOn('/dashboard/login')
            ->assertSeeIn(
                '[data-slot=alert-title]',
                $translator->trans(id: 'Invalid credentials.', domain: 'security')
            )
        ;
    }
}
