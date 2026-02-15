<?php

declare(strict_types=1);

namespace App\Tests\Integration\Experience\Ui\Controller\Dashboard;

use App\User\Test\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Browser\Test\HasBrowser;

final class IndexControllerTest extends WebTestCase
{
    use HasBrowser;

    public function testItCanViewExperiencePage(): void
    {
        $user = UserFactory::createOne();

        self::ensureKernelShutdown();

        $translator = self::getContainer()->get(TranslatorInterface::class);

        $this->browser()
            ->actingAs($user)
            ->visit('/dashboard/experience')
            ->assertAuthenticated($user)
            ->assertSuccessful()
            ->assertSeeIn('head title', \sprintf(
                '%s | %s',
                $translator->trans('dashboard.experience.index.title.page'),
                $translator->trans('app.meta.title')
            ))
            ->assertElementCount('table > tbody > tr', 6)
        ;
    }

    public function testItCanNotViewExperiencePage(): void
    {
        $this->browser()
            ->interceptRedirects()
            ->visit('/dashboard/experience')
            ->assertRedirectedTo('/dashboard/login');
    }
}
