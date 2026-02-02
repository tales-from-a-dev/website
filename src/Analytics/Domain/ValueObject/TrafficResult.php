<?php

declare(strict_types=1);

namespace App\Analytics\Domain\ValueObject;

final class TrafficResult
{
    public function __construct(
        public int $score,
        public string $url,
        public string $method,
        public string $server,
        public string $ip,
        public string $userAgent,
        public ?string $referer = null,
        public bool $verifiedCrawler = false,
    ) {
    }

    public function isBot(): bool
    {
        return $this->score >= 5;
    }
}
