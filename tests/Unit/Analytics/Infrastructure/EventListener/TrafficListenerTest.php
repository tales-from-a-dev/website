<?php

declare(strict_types=1);

namespace App\Tests\Unit\Analytics\Infrastructure\EventListener;

use App\Analytics\Application\Message\ProcessTrafficMessage;
use App\Analytics\Infrastructure\EventListener\TrafficListener;
use App\Analytics\Infrastructure\Helper\TrafficDetector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class TrafficListenerTest extends TestCase
{
    private TrafficDetector $trafficDetector;
    private MessageBusInterface $messageBus;
    private TrafficListener $listener;

    protected function setUp(): void
    {
        $cache = new ArrayAdapter();

        $this->trafficDetector = new TrafficDetector($cache);
        $this->messageBus = $this->createMock(MessageBusInterface::class);

        $this->listener = new TrafficListener(
            trafficDetector: $this->trafficDetector,
            messageBus: $this->messageBus
        );
    }

    public function testItDoesNothingWhenItIsABot(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);

        // Request that TrafficDetector should classify as bot (score >= 5)
        $request = Request::create(
            uri: '/_wdt',
            server: [
                'REMOTE_ADDR' => '1.2.3.4',
            ]
        );

        $response = new Response();

        $event = new TerminateEvent($kernel, $request, $response);

        $this->messageBus
            ->expects($this->never())
            ->method('dispatch');

        ($this->listener)($event);
    }

    public function testItDispatchesProcessTrafficMessageWhenItIsAValidRequest(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);

        // Human-like request (score < 5)
        $request = Request::create(uri: '/blog',
            server: [
                'REMOTE_ADDR' => '9.9.9.9',
            ]
        );
        $request->headers->set('User-Agent', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120 Safari/537.36');
        $request->headers->set('Accept', 'text/html');
        $request->headers->set('Accept-Language', 'en-US');
        $request->headers->set('Accept-Encoding', 'gzip');
        $request->headers->set('Referer', 'https://google.com');
        $request->attributes->set('_route', 'app_blog_show');
        $request->cookies->set('js_enabled', '1');

        $response = new Response();

        $event = new TerminateEvent($kernel, $request, $response);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static function (ProcessTrafficMessage $message) use ($request) {
                return $message->url === $request->getPathInfo() &&
                    $message->method === $request->getMethod() &&
                    $message->server === $request->server->get('SERVER_NAME', '') &&
                    $message->ip === IpUtils::anonymize($request->getClientIp() ?? '') &&
                    $message->userAgent === $request->headers->get('User-Agent') &&
                    $message->referer === $request->headers->get('Referer') &&
                    $message->visitedAt instanceof \DateTimeImmutable;
            }))
            ->willReturn(new Envelope(new \stdClass()));

        ($this->listener)($event);
    }
}
