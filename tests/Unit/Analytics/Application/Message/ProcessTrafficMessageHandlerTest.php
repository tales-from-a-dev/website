<?php

declare(strict_types=1);

namespace App\Tests\Unit\Analytics\Application\Message;

use App\Analytics\Application\Message\ProcessTrafficMessage;
use App\Analytics\Application\Message\ProcessTrafficMessageHandler;
use App\Analytics\Domain\Entity\PageView;
use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ProcessTrafficMessageHandlerTest extends TestCase
{
    public function testItProcessesTrafficMessage(): void
    {
        $pageViewRepository = $this->createMock(PageViewRepositoryInterface::class);

        $handler = new ProcessTrafficMessageHandler($pageViewRepository);

        $message = new ProcessTrafficMessage(
            url: 'https://example.com',
            method: 'GET',
            server: 'localhost',
            ip: '192.168.1.1',
            userAgent: 'Mozilla/5.0',
            referer: 'https://google.com',
            visitedAt: new \DateTimeImmutable('2026-02-16 16:00:00'),
        );

        $pageViewRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(static function (PageView $pageView) use ($message) {
                return $pageView->url === $message->url &&
                    $pageView->method === $message->method &&
                    $pageView->server === $message->server &&
                    '192.168.1.0' === $pageView->ip && // Anonymized
                    $pageView->userAgent === $message->userAgent &&
                    $pageView->referer === $message->referer &&
                    $pageView->visitedAt === $message->visitedAt;
            }));

        ($handler)($message);
    }
}
