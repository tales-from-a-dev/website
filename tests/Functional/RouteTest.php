<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Pierstoval\SmokeTesting\FunctionalSmokeTester;
use Pierstoval\SmokeTesting\FunctionalTestData;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteTest extends WebTestCase
{
    use FunctionalSmokeTester;

    public function testRouteAppWebsiteBlogIndexFrWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/blog')
                ->withMethod('GET')
                ->expectRouteName('app_website_blog_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/blog'))
        );
    }

    public function testRouteAppWebsiteBlogIndexEnWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/blog')
                ->withMethod('GET')
                ->expectRouteName('app_website_blog_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/en/blog'))
        );
    }

    public function testRouteAppWebsiteContactIndexFrWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/contact')
                ->withMethod('GET')
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/contact'))
        );
    }

    public function testRouteAppWebsiteContactIndexFrWithMethodPost(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/contact')
                ->withMethod('POST')
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('POST', '/contact'))
        );
    }

    public function testRouteAppWebsiteContactIndexEnWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/contact')
                ->withMethod('GET')
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/en/contact'))
        );
    }

    public function testRouteAppWebsiteContactIndexEnWithMethodPost(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/contact')
                ->withMethod('POST')
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('POST', '/en/contact'))
        );
    }

    public function testRouteAppWebsiteProjectIndexEnWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/projects')
                ->withMethod('GET')
                ->expectRouteName('app_website_project_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/en/projects'))
        );
    }

    public function testRouteAppWebsiteProjectIndexFrWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/projets')
                ->withMethod('GET')
                ->expectRouteName('app_website_project_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/projets'))
        );
    }

    public function testRouteBlogIndexWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/blog')
                ->withMethod('GET')
                ->expectRouteName('app_website_blog_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/blog'))
        );
    }

    public function testRouteAppWebsiteHomeWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/')
                ->withMethod('GET')
                ->expectRouteName('app_website_home')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500('GET', '/'))
        );
    }

    public function assertStatusCodeLessThan500(string $method, string $url): \Closure
    {
        return function (KernelBrowser $browser) use ($method, $url) {
            $statusCode = $browser->getResponse()->getStatusCode();
            $routeName = $browser->getRequest()->attributes->get('_route', 'unknown');

            static::assertLessThan(
                500,
                $statusCode,
                sprintf('Request "%s %s" for %s route returned an internal error.', $method, $url, $routeName),
            );
        };
    }
}
