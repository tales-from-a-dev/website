<?php

declare(strict_types=1);

namespace App\Tests\Unit\Analytics\Domain\Entity;

use App\Analytics\Domain\Entity\PageView;
use PHPUnit\Framework\TestCase;

final class PageViewTest extends TestCase
{
    public function testItKeepsValuesThatFitTheColumns(): void
    {
        $pageView = new PageView(
            url: '/blog/hello-world',
            method: 'GET',
            server: 'talesfromadev.com',
            ip: '192.168.1.0',
            userAgent: 'Mozilla/5.0',
            referer: 'https://google.com',
            visitedAt: new \DateTimeImmutable('2026-02-16 16:00:00'),
        );

        self::assertSame('/blog/hello-world', $pageView->url);
        self::assertSame('GET', $pageView->method);
        self::assertSame('talesfromadev.com', $pageView->server);
        self::assertSame('192.168.1.0', $pageView->ip);
        self::assertSame('Mozilla/5.0', $pageView->userAgent);
        self::assertSame('https://google.com', $pageView->referer);
    }

    public function testItTruncatesValuesTooLongForTheColumns(): void
    {
        $pageView = new PageView(
            url: '/'.str_repeat('a', 300),
            method: str_repeat('B', 20),
            server: str_repeat('c', 300),
            ip: str_repeat('4', 300),
            userAgent: str_repeat('d', 300),
            referer: 'https://google.com/?q='.str_repeat('e', 300),
            visitedAt: new \DateTimeImmutable('2026-02-16 16:00:00'),
        );

        self::assertSame('/'.str_repeat('a', 254), $pageView->url);
        self::assertSame(str_repeat('B', 10), $pageView->method);
        self::assertSame(str_repeat('c', 255), $pageView->server);
        self::assertSame(str_repeat('4', 255), $pageView->ip);
        self::assertSame(str_repeat('d', 255), $pageView->userAgent);
        self::assertSame('https://google.com/?q='.str_repeat('e', 233), $pageView->referer);
    }

    public function testItTruncatesOnCodePointsRatherThanBytes(): void
    {
        $pageView = new PageView(
            url: '/'.str_repeat('é', 300),
            method: 'GET',
            server: 'talesfromadev.com',
            ip: '192.168.1.0',
            userAgent: 'Mozilla/5.0',
            referer: null,
            visitedAt: new \DateTimeImmutable('2026-02-16 16:00:00'),
        );

        self::assertSame('/'.str_repeat('é', 254), $pageView->url);
        self::assertSame(255, mb_strlen($pageView->url, 'UTF-8'));
    }

    public function testItKeepsANullReferer(): void
    {
        $pageView = new PageView(
            url: '/blog/hello-world',
            method: 'GET',
            server: 'talesfromadev.com',
            ip: '192.168.1.0',
            userAgent: 'Mozilla/5.0',
            referer: null,
            visitedAt: new \DateTimeImmutable('2026-02-16 16:00:00'),
        );

        self::assertNull($pageView->referer);
    }
}
