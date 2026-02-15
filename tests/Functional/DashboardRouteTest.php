<?php

declare(strict_types=1);

use App\Experience\Domain\Enum\ExperienceRouteNameEnum;
use App\Settings\Domain\Enum\SettingsRouteNameEnum;
use App\Shared\Domain\Enum\SharedRouteNameEnum;
use App\Tests\RouteTestCase;
use App\User\Domain\Enum\UserRouteNameEnum;
use Symfony\Component\HttpFoundation\Request;

final class DashboardRouteTest extends RouteTestCase
{
    /**
     * @return iterable<array<int, mixed>>
     */
    #[Override]
    public static function urlsProvider(): iterable
    {
        yield 'GET /dashboard' => ['/dashboard', Request::METHOD_GET, SharedRouteNameEnum::DashboardIndex];

        yield 'GET /dashboard/login' => ['/dashboard/login', Request::METHOD_GET, UserRouteNameEnum::DashboardLogin];
        yield 'POST /dashboard/login' => ['/dashboard/login', Request::METHOD_POST, UserRouteNameEnum::DashboardLogin];

        yield 'GET /dashboard/settings' => ['/dashboard/settings', Request::METHOD_GET, SettingsRouteNameEnum::DashboardSettings];
        yield 'POST /dashboard/settings' => ['/dashboard/settings', Request::METHOD_POST, SettingsRouteNameEnum::DashboardSettings];

        yield 'GET /dashboard/experience' => ['/dashboard/experience', Request::METHOD_GET, ExperienceRouteNameEnum::DashboardIndex];
        yield 'GET /dashboard/experience/new' => ['/dashboard/experience/new', Request::METHOD_GET, ExperienceRouteNameEnum::DashboardNew];
        yield 'POST /dashboard/experience/new' => ['/dashboard/experience/new', Request::METHOD_POST, ExperienceRouteNameEnum::DashboardNew];
        yield 'GET /dashboard/experience/edit/1' => ['/dashboard/experience/edit/1', Request::METHOD_GET, ExperienceRouteNameEnum::DashboardEdit];
        yield 'POST /dashboard/experience/edit/1' => ['/dashboard/experience/edit/1', Request::METHOD_POST, ExperienceRouteNameEnum::DashboardEdit];
    }
}
