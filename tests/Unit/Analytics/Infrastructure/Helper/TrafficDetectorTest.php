<?php

declare(strict_types=1);

namespace App\Tests\Unit\Analytics\Infrastructure\Helper;

use App\Analytics\Infrastructure\Helper\TrafficDetector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

final class TrafficDetectorTest extends TestCase
{
    private CacheInterface $cache;

    private TrafficDetector $trafficDetector;

    protected function setUp(): void
    {
        $this->cache = $this->createStub(CacheInterface::class);

        $this->trafficDetector = new TrafficDetector(
            cache: $this->cache
        );
    }

    public function testDetectWithStandardRequest(): void
    {
        $request = Request::create('/home');
        $request->headers->set('User-Agent', 'Mozilla/5.0');
        $request->headers->set('Accept', 'text/html');
        $request->headers->set('Accept-Encoding', 'gzip, deflate');
        $request->headers->set('Referer', 'https://google.com');
        $request->cookies->set('js_enabled', '1');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $request->attributes->set('_route', 'home');

        $this->cache
            ->method('get')
            ->willReturn(['count' => 1, 'last' => microtime(true)])
        ;

        $result = $this->trafficDetector->detect($request);

        $this->assertEquals(0, $result->score);
        $this->assertEquals('/home', $result->url);
        $this->assertEquals('GET', $result->method);
        $this->assertEquals('1.1.1.0', $result->ip); // Anonymized
    }

    public function testDetectWithBlacklistedUrl(): void
    {
        $request = Request::create('/_wdt/some-token');
        $request->headers->set('User-Agent', 'Mozilla/5.0');
        $request->headers->set('Accept', 'text/html');
        $request->headers->set('Accept-Encoding', 'gzip, deflate');
        $request->headers->set('Referer', 'https://google.com');
        $request->cookies->set('js_enabled', '1');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $request->attributes->set('_route', 'wdt');

        $this->cache
            ->method('get')
            ->willReturn(['count' => 1, 'last' => microtime(true)])
        ;

        $result = $this->trafficDetector->detect($request);

        // URL score: 1
        $this->assertEquals(5, $result->score);
    }

    public function testDetectWithBlacklistedUserAgent(): void
    {
        $request = Request::create('/home');
        $request->headers->set('User-Agent', 'Scrapy');
        $request->headers->set('Accept', 'text/html');
        $request->headers->set('Accept-Encoding', 'gzip, deflate');
        $request->headers->set('Referer', 'https://google.com');
        $request->cookies->set('js_enabled', '1');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $request->attributes->set('_route', 'home');

        $this->cache
            ->method('get')
            ->willReturn(['count' => 1, 'last' => microtime(true)])
        ;

        $result = $this->trafficDetector->detect($request);

        // User agent score: 3
        $this->assertEquals(3, $result->score);
    }

    public function testDetectWithBlacklistedIp(): void
    {
        $request = Request::create('/home');
        $request->headers->set('User-Agent', 'Mozilla/5.0');
        $request->headers->set('Accept', 'text/html');
        $request->headers->set('Accept-Encoding', 'gzip, deflate');
        $request->headers->set('Referer', 'https://google.com');
        $request->cookies->set('js_enabled', '1');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $request->attributes->set('_route', 'home');

        $this->cache
            ->method('get')
            ->willReturn(['count' => 1, 'last' => microtime(true)])
        ;

        $result = $this->trafficDetector->detect($request);

        // IP score: 3
        $this->assertEquals(3, $result->score);
    }

    public function testDetectWithMissingHeadersAndNoCookies(): void
    {
        $request = Request::create('/home');
        $request->headers->set('User-Agent', 'Mozilla/5.0');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $request->attributes->set('_route', 'home');

        $request->headers->remove('Accept');

        $this->cache
            ->method('get')
            ->willReturn(['count' => 1, 'last' => microtime(true)])
        ;

        $result = $this->trafficDetector->detect($request);

        // headerScore (>= 2 missing): 2
        // browserScore (no cookies): 1
        // browserScore (no js_enabled): 2
        // Total: 5
        $this->assertEquals(5, $result->score);
    }

    public function testDetectWithHighRate(): void
    {
        $request = Request::create('/home');
        $request->headers->set('User-Agent', 'Mozilla/5.0');
        $request->headers->set('Accept', 'text/html');
        $request->headers->set('Accept-Encoding', 'gzip, deflate');
        $request->headers->set('Referer', 'https://google.com');
        $request->cookies->set('js_enabled', '1');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $request->attributes->set('_route', 'home');

        $this->cache
            ->method('get')
            ->willReturn(['count' => 31, 'last' => microtime(true)])
        ;

        $result = $this->trafficDetector->detect($request);

        // rateScore (> 30): 3
        $this->assertEquals(3, $result->score);
    }

    public function testDetectWithMediumRate(): void
    {
        $request = Request::create('/home');
        $request->headers->set('User-Agent', 'Mozilla/5.0');
        $request->headers->set('Accept', 'text/html');
        $request->headers->set('Accept-Encoding', 'gzip, deflate');
        $request->headers->set('Referer', 'https://google.com');
        $request->cookies->set('js_enabled', '1');
        $request->server->set('REMOTE_ADDR', '1.1.1.1');
        $request->attributes->set('_route', 'home');

        $this->cache
            ->method('get')
            ->willReturn(['count' => 11, 'last' => microtime(true)])
        ;

        $result = $this->trafficDetector->detect($request);

        // rateScore (> 10): 1
        $this->assertEquals(1, $result->score);
    }
}
