<?php

declare(strict_types=1);

namespace App\Tests\Integration\Settings\Ui\Controller;

use App\Settings\Infrastructure\Repository\SettingsRepository;
use App\User\Test\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;

final class SettingsControllerTest extends WebTestCase
{
    use Factories;
    use HasBrowser;

    public function testItCanViewSettingsPage(): void
    {
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->actingAs($user)
            ->visit('/settings')
            ->assertAuthenticated($user)
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('settings.title'),
                $translator->trans('app.meta.title')
            ));
    }

    public function testItCanUpdateSettings(): void
    {
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $this->browser()
            ->actingAs($user)
            ->visit('/settings')
            ->uncheckField('settings_available')
            ->fillField('settings_availableAt', \IntlDateFormatter::formatObject($availableAt = new \DateTimeImmutable('tomorrow'), 'yyyy-MM-dd'))
            ->fillField('settings_averageDailyRate', '100')
            ->click('submit')
            ->assertSuccessful();

        $settings = self::getContainer()->get(SettingsRepository::class)->findFirst();

        $this->assertFalse($settings->available);
        $this->assertSame($availableAt->format(\DateTimeInterface::ATOM), $settings->availableAt?->format(\DateTimeInterface::ATOM));
        $this->assertSame(100, $settings->averageDailyRate);
    }

    public function testItTriggerErrorsWithInvalidData(): void
    {
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $translator = self::getContainer()->get(TranslatorInterface::class);

        $formatter = new \IntlDateFormatter(\Locale::getDefault(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT, 'UTC');

        $this->browser()
            ->actingAs($user)
            ->visit('/settings')
            ->uncheckField('settings_available')
            ->fillField('settings_availableAt', \IntlDateFormatter::formatObject(new \DateTimeImmutable('yesterday'), 'yyyy-MM-dd'))
            ->fillField('settings_averageDailyRate', '-100')
            ->click('submit')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertElementCount('p[id=settings_availableAt_error_0]', 1)
            ->assertSeeIn(
                'p[id=settings_availableAt_error_0]',
                $translator->trans(
                    id: 'This value should be greater than or equal to {{ compared_value }}.',
                    parameters: [
                        '{{ compared_value }}' => $formatter->format(new \DateTimeImmutable('today', new \DateTimeZone('UTC'))),
                    ],
                    domain: 'validators',
                )
            )
            ->assertElementCount('p[id=settings_averageDailyRate_error_0]', 1)
            ->assertSeeIn(
                'p[id=settings_averageDailyRate_error_0]',
                $translator->trans(
                    id: 'This value should be positive.',
                    domain: 'validators',
                )
            )
        ;
    }
}
