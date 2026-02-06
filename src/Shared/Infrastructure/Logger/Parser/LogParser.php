<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Logger\Parser;

use App\Shared\Domain\Logger\Parser\LogParserInterface;
use App\Shared\Domain\ValueObject\LogEntry;

final class LogParser implements LogParserInterface
{
    // [2026-01-29T14:28:37.851112+00:00] request.INFO: Matched route "app_shared_home". {"route":"...","...":"..."} {"url":"/","ip":"172.19.0.1",...}
    private const string LOG_PATTERN = '/^\[(?P<datetime>.*)\] (?P<channel>\w+)\.(?P<level>\w+): (?P<message>.*) (?P<context>\{.*\}) (?P<extra>\{.*\})$/';

    /**
     * @return iterable<LogEntry>
     */
    public function parse(string $filePath): iterable
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(\sprintf('File "%s" does not exist.', $filePath));
        }

        $handle = fopen($filePath, 'r');
        if (false === $handle) {
            throw new \RuntimeException(\sprintf('Could not open file "%s" for reading.', $filePath));
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ('' === $line) {
                    continue;
                }

                $entry = $this->parseLine($line);
                if (null !== $entry) {
                    yield $entry;
                }
            }
        } finally {
            fclose($handle);
        }
    }

    public function parseLine(string $line): ?LogEntry
    {
        if (!preg_match(self::LOG_PATTERN, $line, $matches)) {
            return null;
        }

        try {
            return new LogEntry(
                datetime: new \DateTimeImmutable($matches['datetime']),
                channel: $matches['channel'],
                level: $matches['level'],
                message: $matches['message'],
                context: json_decode($matches['context'], true, 512, \JSON_THROW_ON_ERROR),
                extra: json_decode($matches['extra'], true, 512, \JSON_THROW_ON_ERROR),
            );
        } catch (\Exception) {
            return null;
        }
    }
}
