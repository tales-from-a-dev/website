<?php

declare(strict_types=1);

namespace App\Analytics\Application\Message;

use App\Analytics\Domain\Entity\PageView;
use App\Analytics\Domain\Repository\PageViewRepositoryInterface;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProcessTrafficMessageHandler
{
    public function __construct(
        private PageViewRepositoryInterface $pageViewRepository,
    ) {
    }

    public function __invoke(ProcessTrafficMessage $message): void
    {
        $pageView = new PageView(
            url: $message->url,
            method: $message->method,
            server: $message->server,
            ip: IpUtils::anonymize($message->ip),
            userAgent: $message->userAgent,
            referer: $message->referer,
            visitedAt: $message->visitedAt,
        );

        $this->pageViewRepository->add($pageView);
    }
}
