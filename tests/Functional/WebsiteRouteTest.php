<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Contact\Domain\Enum\ContactRouteNameEnum;
use App\Shared\Domain\Enum\SharedRouteNameEnum;
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
        yield 'GET /' => ['/', Request::METHOD_GET, SharedRouteNameEnum::WebsiteIndex];

        yield 'GET /contact' => ['/contact', Request::METHOD_GET, ContactRouteNameEnum::WebsiteContact];
        yield 'POST /contact' => ['/contact', Request::METHOD_POST, ContactRouteNameEnum::WebsiteContact];
    }
}
