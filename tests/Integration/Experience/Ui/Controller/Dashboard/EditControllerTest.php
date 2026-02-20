<?php

declare(strict_types=1);

namespace App\Tests\Integration\Experience\Ui\Controller\Dashboard;

use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use App\Experience\Test\Factory\ExperienceFactory;
use App\User\Test\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class EditControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewExperienceEditPage(): void
    {
        $experience = ExperienceFactory::createOne();
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->actingAs($user)
            ->visit("/dashboard/experience/edit/$experience->id")
            ->assertAuthenticated($user)
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('dashboard.experience.edit.title.page'),
                $translator->trans('app.meta.title')
            ));
    }

    public function testItCanNotViewExperienceEditPage(): void
    {
        $experience = ExperienceFactory::createOne();

        self::ensureKernelShutdown();

        $this->browser()
            ->interceptRedirects()
            ->visit("/dashboard/experience/edit/$experience->id")
            ->assertRedirectedTo('/dashboard/login');
    }

    public function testItCanUpdateExperience(): void
    {
        $experience = ExperienceFactory::createOne();
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->actingAs($user)
            ->visit("/dashboard/experience/edit/$experience->id")
            ->fillField('experience_company', 'Facebook')
            ->selectField('experience_type', ExperienceTypeEnum::PermanentContract->value)
            ->selectField('experience_position', ExperiencePositionEnum::BackendDeveloper->value)
            ->fillField('experience_startAt', \IntlDateFormatter::formatObject($startAt = new \DateTimeImmutable('today last year'), 'yyyy-MM-dd'))
            ->fillField('experience_endAt', \IntlDateFormatter::formatObject($endAt = new \DateTimeImmutable('today'), 'yyyy-MM-dd'))
            ->click('submit')
            ->assertSuccessful()
            ->assertSeeIn(
                'div[data-slot=alert-title]',
                $translator->trans(
                    id: 'experience.update.success',
                    domain: 'alert'
                )
            )
        ;

        $this->assertSame($startAt->format(\DateTimeInterface::ATOM), $experience->startAt->format(\DateTimeInterface::ATOM));
        $this->assertSame($endAt->format(\DateTimeInterface::ATOM), $experience->endAt?->format(\DateTimeInterface::ATOM));
        $this->assertSame('Facebook', $experience->company);
        $this->assertSame(ExperienceTypeEnum::PermanentContract, $experience->type);
        $this->assertSame(ExperiencePositionEnum::BackendDeveloper, $experience->position);
    }

    public function testItTriggerErrorsWithInvalidData(): void
    {
        $experience = ExperienceFactory::createOne();
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $translator = self::getContainer()->get(TranslatorInterface::class);

        $formatter = new \IntlDateFormatter(\Locale::getDefault(), \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT, 'UTC');

        $this->browser()
            ->actingAs($user)
            ->visit("/dashboard/experience/edit/$experience->id")
            ->fillField('experience_startAt', \IntlDateFormatter::formatObject($startAt = new \DateTimeImmutable('today last year'), 'yyyy-MM-dd'))
            ->fillField('experience_endAt', \IntlDateFormatter::formatObject($startAt->modify('-2 days'), 'yyyy-MM-dd'))
            ->click('submit')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertElementCount('p[id=experience_endAt_error_0]', 1)
            ->assertSeeIn(
                'p[id=experience_endAt_error_0]',
                $translator->trans(
                    id: 'This value should be greater than {{ compared_value }}.',
                    parameters: [
                        '{{ compared_value }}' => $formatter->format($startAt),
                    ],
                    domain: 'validators',
                )
            )
        ;
    }
}
