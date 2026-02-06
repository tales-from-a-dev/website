<?php

declare(strict_types=1);

namespace App\Shared\Domain\Logger\Parser;

use App\Shared\Domain\ValueObject\LogEntry;

interface LogParserInterface
{
    /**
     * @return iterable<LogEntry>
     */
    public function parse(string $filePath): iterable;

    public function parseLine(string $line): ?LogEntry;
}
