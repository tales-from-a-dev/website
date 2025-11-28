<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Contact\Domain\Enum\ContactRouteNameEnum;
use App\Settings\Domain\Enum\SettingsRouteNameEnum;
use App\Shared\Domain\Enum\SharedRouteNameEnum;
use App\Tests\RouteTestCase;
use App\User\Domain\Enum\UserRouteNameEnum;
use Symfony\Component\HttpFoundation\Request;

final class WebsiteRouteTest extends RouteTestCase
{
    /**
     * @return iterable<array<int, mixed>>
     */
    #[\Override]
    public static function urlsProvider(): iterable
    {
        yield 'GET /' => ['/', Request::METHOD_GET, SharedRouteNameEnum::WebsiteHome];

        yield 'GET /contact' => ['/contact', Request::METHOD_GET, ContactRouteNameEnum::WebsiteContact];
        yield 'POST /contact' => ['/contact', Request::METHOD_POST, ContactRouteNameEnum::WebsiteContact];

        yield 'GET /login' => ['/login', Request::METHOD_GET, UserRouteNameEnum::WebsiteLogin];
        yield 'POST /login' => ['/login', Request::METHOD_POST, UserRouteNameEnum::WebsiteLogin];

        yield 'GET /settings' => ['/settings', Request::METHOD_GET, SettingsRouteNameEnum::WebsiteSettings];
        yield 'POST /settings' => ['/settings', Request::METHOD_POST, SettingsRouteNameEnum::WebsiteSettings];
    }
}
