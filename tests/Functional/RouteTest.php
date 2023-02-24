<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Pierstoval\SmokeTesting\FunctionalSmokeTester;
use Pierstoval\SmokeTesting\FunctionalTestData;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class RouteTest extends WebTestCase
{
    use FunctionalSmokeTester;

    public function testRouteAppWebsiteBlogIndexFrWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/blog')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_blog_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/blog'))
        );
    }

    public function testRouteAppWebsiteBlogIndexEnWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/blog')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_blog_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/en/blog'))
        );
    }

    public function testRouteAppWebsiteContactIndexFrWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/contact')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/contact'))
        );
    }

    public function testRouteAppWebsiteContactIndexFrWithMethodPost(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/contact')
                ->withMethod(Request::METHOD_POST)
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_POST, '/contact'))
        );
    }

    public function testRouteAppWebsiteContactIndexEnWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/contact')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/en/contact'))
        );
    }

    public function testRouteAppWebsiteContactIndexEnWithMethodPost(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/contact')
                ->withMethod(Request::METHOD_POST)
                ->expectRouteName('app_website_contact_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_POST, '/en/contact'))
        );
    }

    public function testRouteAppWebsiteProjectIndexEnWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/en/projects')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_project_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/en/projects'))
        );
    }

    public function testRouteAppWebsiteProjectIndexFrWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/projets')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_project_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/projets'))
        );
    }

    public function testRouteBlogIndexWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/blog')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_blog_index')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/blog'))
        );
    }

    public function testRouteAppWebsiteHomeWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_website_home')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/'))
        );
    }

    public function testRouteAppAdminLoginWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/login')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_admin_login')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/'))
        );
    }

    public function testRouteAppAdminLoginWithMethodPost(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/login')
                ->withMethod(Request::METHOD_POST)
                ->expectRouteName('app_admin_login')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_POST, '/'))
        );
    }

    public function testRouteAppAdminLogoutWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin/logout')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_admin_logout')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/'))
        );
    }

    public function testRouteAppAdminHomeWithMethodGet(): void
    {
        $this->runFunctionalTest(
            FunctionalTestData::withUrl('/admin')
                ->withMethod(Request::METHOD_GET)
                ->expectRouteName('app_admin_home')
                ->appendCallableExpectation($this->assertStatusCodeLessThan500(Request::METHOD_GET, '/'))
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
