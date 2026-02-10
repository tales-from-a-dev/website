<?php

declare(strict_types=1);

namespace App\Tests\Unit\Analytics\Application\Message;

use App\Analytics\Application\Message\ProcessDailyTrafficMessage;
use App\Analytics\Application\Message\ProcessDailyTrafficMessageHandler;
use App\Analytics\Domain\Entity\PageView;
use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use App\Shared\Domain\Logger\Parser\LogParserInterface;
use App\Shared\Domain\ValueObject\LogEntry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class ProcessDailyTrafficMessageHandlerTest extends TestCase
{
    private LogParserInterface&MockObject $logParser;
    private CacheInterface&MockObject $cache;
    private PageViewRepositoryInterface&MockObject $pageViewRepository;
    private string $logsDir;

    private ProcessDailyTrafficMessageHandler $handler;

    protected function setUp(): void
    {
        $this->logParser = $this->createMock(LogParserInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->pageViewRepository = $this->createMock(PageViewRepositoryInterface::class);
        $this->logsDir = sys_get_temp_dir().'/test_logs_'.uniqid();

        mkdir($this->logsDir);

        $this->handler = new ProcessDailyTrafficMessageHandler(
            logParser: $this->logParser,
            analyticsCache: $this->cache,
            pageViewRepository: $this->pageViewRepository,
            logsDir: $this->logsDir
        );
    }

    protected function tearDown(): void
    {
        if (is_dir($this->logsDir)) {
            $files = glob($this->logsDir.'/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->logsDir);
        }
    }

    public function testItProcessesValidLogFile(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $logEntry = new LogEntry(
            datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
            channel: 'request',
            level: 'INFO',
            message: 'Request processed',
            context: [],
            extra: [
                'http_method' => 'GET',
                'url' => '/blog/article',
                'user_agent' => 'Mozilla/5.0',
                'ip' => '192.168.1.1',
                'server' => 'localhost',
                'referrer' => 'https://google.com',
            ]
        );

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->with($logFile)
            ->willReturn([$logEntry])
        ;

        $this->pageViewRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(PageView::class))
        ;

        $this->cache
            ->expects($this->once())
            ->method('delete')
            ->with('last_traffic_parsed_files')
        ;

        $message = new ProcessDailyTrafficMessage();
        $result = ($this->handler)($message);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('traffic-2026-02-05.log', $result[0]);
    }

    public function testItSkipsAlreadyParsedFiles(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                if ('last_traffic_parsed_files' === $key) {
                    return ['traffic-2026-02-05.log'];
                }

                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->never())
            ->method('parse')
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $this->cache
            ->expects($this->once())
            ->method('delete')
            ->with('last_traffic_parsed_files')
        ;

        $message = new ProcessDailyTrafficMessage();
        $result = ($this->handler)($message);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('traffic-2026-02-05.log', $result[0]);
    }

    public function testItFiltersNonInfoLogLevel(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $logEntry = new LogEntry(
            datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
            channel: 'request',
            level: 'ERROR',
            message: 'Error occurred',
            context: [],
            extra: [
                'http_method' => 'GET',
                'url' => '/blog/article',
                'user_agent' => 'Mozilla/5.0',
                'ip' => '192.168.1.1',
            ]
        );

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn([$logEntry])
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $message = new ProcessDailyTrafficMessage();
        ($this->handler)($message);
    }

    public function testItFiltersEmptyHttpMethod(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $logEntry = new LogEntry(
            datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
            channel: 'request',
            level: 'INFO',
            message: 'Request processed',
            context: [],
            extra: [
                'url' => '/blog/article',
                'user_agent' => 'Mozilla/5.0',
                'ip' => '192.168.1.1',
            ]
        );

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn([$logEntry])
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $message = new ProcessDailyTrafficMessage();
        ($this->handler)($message);
    }

    public function testItFiltersBlacklistedUrls(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $blacklistedUrls = [
            '/style.css',
            '/script.js',
            '/image.png',
            '/_wdt/123',
            '/_profiler/abc',
            '/contact',
            '/settings',
            '/login',
            '/robots.txt',
        ];

        $logEntries = [];
        foreach ($blacklistedUrls as $url) {
            $logEntries[] = new LogEntry(
                datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
                channel: 'request',
                level: 'INFO',
                message: 'Request processed',
                context: [],
                extra: [
                    'http_method' => 'GET',
                    'url' => $url,
                    'user_agent' => 'Mozilla/5.0',
                    'ip' => '192.168.1.1',
                ]
            );
        }

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn($logEntries)
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $message = new ProcessDailyTrafficMessage();
        ($this->handler)($message);
    }

    public function testItFiltersBlacklistedUserAgents(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $blacklistedUserAgents = [
            'Googlebot',
            'crawler/1.0',
            'spider-bot',
            'Scrapy/1.0',
            'curl/7.0',
            'Wget/1.0',
            'HttpClient/1.0',
            'Python-urllib',
            'Java/1.8',
            'Go-http-client/1.0',
            'libwww-perl',
        ];

        $logEntries = [];
        foreach ($blacklistedUserAgents as $userAgent) {
            $logEntries[] = new LogEntry(
                datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
                channel: 'request',
                level: 'INFO',
                message: 'Request processed',
                context: [],
                extra: [
                    'http_method' => 'GET',
                    'url' => '/blog/article',
                    'user_agent' => $userAgent,
                    'ip' => '192.168.1.1',
                ]
            );
        }

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn($logEntries)
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $message = new ProcessDailyTrafficMessage();
        ($this->handler)($message);
    }

    public function testItFiltersEmptyIp(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $logEntry = new LogEntry(
            datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
            channel: 'request',
            level: 'INFO',
            message: 'Request processed',
            context: [],
            extra: [
                'http_method' => 'GET',
                'url' => '/blog/article',
                'user_agent' => 'Mozilla/5.0',
            ]
        );

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn([$logEntry])
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $message = new ProcessDailyTrafficMessage();
        ($this->handler)($message);
    }

    public function testItHandlesParsingExceptions(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('last_traffic_parsed_files')
            ->willReturn([])
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->willThrowException(new \RuntimeException('Parse error'))
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $this->cache
            ->expects($this->never())
            ->method('delete')
        ;

        $message = new ProcessDailyTrafficMessage();
        $result = ($this->handler)($message);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testItProcessesMultipleLogFiles(): void
    {
        $logFile1 = $this->logsDir.'/traffic-2026-02-05.log';
        $logFile2 = $this->logsDir.'/traffic-2026-02-04.log';

        touch($logFile1);
        touch($logFile2);

        $logEntry = new LogEntry(
            datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
            channel: 'request',
            level: 'INFO',
            message: 'Request processed',
            context: [],
            extra: [
                'http_method' => 'GET',
                'url' => '/blog/article',
                'user_agent' => 'Mozilla/5.0',
                'ip' => '192.168.1.1',
                'server' => 'localhost',
                'referrer' => '',
            ]
        );

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->exactly(2))
            ->method('parse')
            ->willReturn([$logEntry])
        ;

        $this->pageViewRepository
            ->expects($this->exactly(2))
            ->method('add')
        ;

        $this->cache
            ->expects($this->once())
            ->method('delete')
        ;

        $message = new ProcessDailyTrafficMessage();
        $result = ($this->handler)($message);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testItIgnoresNonMatchingFiles(): void
    {
        $invalidFile1 = $this->logsDir.'/traffic.log';
        $invalidFile2 = $this->logsDir.'/other-2026-02-05.log';

        touch($invalidFile1);
        touch($invalidFile2);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('last_traffic_parsed_files')
            ->willReturn([])
        ;

        $this->logParser
            ->expects($this->never())
            ->method('parse')
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $message = new ProcessDailyTrafficMessage();
        $result = ($this->handler)($message);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testItCreatesPageViewWithNullReferer(): void
    {
        $logFile = $this->logsDir.'/traffic-2026-02-05.log';

        touch($logFile);

        $logEntry = new LogEntry(
            datetime: new \DateTimeImmutable('2026-02-05 10:00:00'),
            channel: 'request',
            level: 'INFO',
            message: 'Request processed',
            context: [],
            extra: [
                'http_method' => 'POST',
                'url' => '/api/endpoint',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0)',
                'ip' => '10.0.0.1',
                'server' => 'example.com',
            ]
        );

        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function ($key, $callback) {
                return $callback($this->createStub(ItemInterface::class));
            })
        ;

        $this->logParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn([$logEntry])
        ;

        $this->pageViewRepository
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(static function (PageView $pageView) {
                return null === $pageView->referer->value;
            }))
        ;

        $message = new ProcessDailyTrafficMessage();
        ($this->handler)($message);
    }

    public function testItReturnsEmptyArrayWhenNoFilesProcessed(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('last_traffic_parsed_files')
            ->willReturn([])
        ;

        $this->logParser
            ->expects($this->never())
            ->method('parse')
        ;

        $this->pageViewRepository
            ->expects($this->never())
            ->method('add')
        ;

        $this->cache
            ->expects($this->never())
            ->method('delete')
        ;

        $message = new ProcessDailyTrafficMessage();
        $result = ($this->handler)($message);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
