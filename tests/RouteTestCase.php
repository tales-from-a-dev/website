<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Pierstoval\SmokeTesting\FunctionalSmokeTester;
use Pierstoval\SmokeTesting\FunctionalTestData;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class RouteTestCase extends WebTestCase
{
    use FunctionalSmokeTester;

    /**
     * @return iterable<int|string, mixed>
     */
    abstract public static function urlsProvider(): iterable;

    #[DataProvider('urlsProvider')]
    public function testUrlIsReachable(string $url, string $method, \BackedEnum|string $routeName): void
    {
        $routeName = \is_string($routeName) ? $routeName : (string) $routeName->value;

        $this->runFunctionalTest(FunctionalTestData::withUrl($url)
            ->withMethod($method)
            ->expectRouteName($routeName)
            ->appendCallableExpectation(self::assertStatusCodeLessThan500($method, $url))
        );
    }

    public static function assertStatusCodeLessThan500(string $method, string $url): \Closure
    {
        return function (KernelBrowser $browser) use ($method, $url) {
            $statusCode = $browser->getResponse()->getStatusCode();
            $routeName = $browser->getRequest()->attributes->get('_route', 'unknown');

            $this->assertLessThan(
                500,
                $statusCode,
                \sprintf('Request "%s %s" for %s route returned an internal error.', $method, $url, $routeName),
            );
        };
    }
}
