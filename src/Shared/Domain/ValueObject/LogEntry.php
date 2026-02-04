<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final readonly class LogEntry
{
    /**
     * @param array{
     *     route: string,
     *     route_parameters: array<string, mixed>,
     *     request_uri: string,
     *     method: string,
     * } $context
     * @param array{
     *     url: string,
     *     ip: string,
     *     http_method: string,
     *     server: string,
     *     referrer: string,
     *     user_agent: string,
     * } $extra
     */
    public function __construct(
        public \DateTimeImmutable $datetime,
        public string $channel,
        public string $level,
        public string $message,
        public array $context,
        public array $extra,
    ) {
    }
}
