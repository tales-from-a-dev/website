<?php

declare(strict_types=1);

namespace App\Analytics\Infrastructure\Logger\Processor;

use Monolog\Processor\WebProcessor as BaseWebProcessor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TrafficProcessor extends BaseWebProcessor implements EventSubscriberInterface
{
    public function __construct()
    {
        parent::__construct([], $this->extraFields);
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->isMainRequest()) {
            $this->serverData = $event->getRequest()->server->all();
            $this->serverData['REMOTE_ADDR'] = $event->getRequest()->getClientIp();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 4096],
        ];
    }
}
