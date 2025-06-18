<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Domain\Enum\RouteNameEnum;
use App\Tests\RouteTestCase;
use Symfony\Component\HttpFoundation\Request;

final class WebsiteRouteTest extends RouteTestCase
{
    /**
     * @return iterable<array<int, mixed>>
     */
    #[\Override]
    public static function urlsProvider(): iterable
    {
        yield 'GET /' => ['/', Request::METHOD_GET, RouteNameEnum::WebsiteHome];

        yield 'GET /contact' => ['/contact', Request::METHOD_GET, RouteNameEnum::WebsiteContact];
        yield 'POST /contact' => ['/contact', Request::METHOD_POST, RouteNameEnum::WebsiteContact];

        yield 'GET /login' => ['/login', Request::METHOD_GET, RouteNameEnum::WebsiteLogin];
        yield 'POST /login' => ['/login', Request::METHOD_POST, RouteNameEnum::WebsiteLogin];

        yield 'GET /settings' => ['/settings', Request::METHOD_GET, RouteNameEnum::WebsiteSettings];
        yield 'POST /settings' => ['/settings', Request::METHOD_POST, RouteNameEnum::WebsiteSettings];
    }
}
