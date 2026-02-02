<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\EventListener;

use App\Analytics\Application\Message\ProcessTrafficMessage;
use App\Analytics\Infrastructure\Helper\TrafficDetector;
use Symfony\Component\Clock\Clock;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener(event: KernelEvents::TERMINATE)]
final readonly class TrafficListener
{
    public function __construct(
        private TrafficDetector $trafficDetector,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(TerminateEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $result = $this->trafficDetector->detect($event->getRequest());

        if ($result->isBot()) {
            return;
        }

        $this->messageBus->dispatch(new ProcessTrafficMessage(
            url: $result->url,
            method: $result->method,
            server: $result->server,
            ip: $result->ip,
            userAgent: $result->userAgent,
            referer: $result->referer,
            visitedAt: Clock::get()->now(),
        ));
    }
}
