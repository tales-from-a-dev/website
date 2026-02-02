<?php

declare(strict_types=1);

namespace App\Analytics\Application\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'async')]
final readonly class ProcessTrafficMessage
{
    public function __construct(
        public string $url,
        public string $method,
        public string $server,
        public string $ip,
        public string $userAgent,
        public ?string $referer,
        public \DateTimeImmutable $visitedAt,
    ) {
    }
}
