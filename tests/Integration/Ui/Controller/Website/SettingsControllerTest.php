<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Controller\Website;

use App\Domain\Enum\UserRoleEnum;
use App\Infrastructure\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class SettingsControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewSettingsPage(): void
    {
        $translator = self::getContainer()->get(TranslatorInterface::class);
        $user = $this->getUser();

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
        $user = $this->getUser();

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
        $translator = self::getContainer()->get(TranslatorInterface::class);
        $user = $this->getUser();

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

    private function getUser(): InMemoryUser
    {
        return new InMemoryUser(
            username: 'user@example.com',
            password: '$2y$13$.HTrY6My5GMKXPtBaAo4yuYxi3w2VvstIOWveXCwjbTusEGc6NR8m',
            roles: [UserRoleEnum::User->value],
        );
    }
}
