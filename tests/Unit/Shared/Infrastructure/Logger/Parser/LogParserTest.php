<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Logger\Parser;

use App\Shared\Domain\ValueObject\LogEntry;
use App\Shared\Infrastructure\Logger\Parser\LogParser;
use PHPUnit\Framework\TestCase;

final class LogParserTest extends TestCase
{
    private LogParser $parser;

    protected function setUp(): void
    {
        $this->parser = new LogParser();
    }

    public function testParseFile(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'log');
        $content = '[2026-01-29T14:28:37.851112+00:00] request.INFO: Message 1 {"ctx":1} {"ext":1}'."\n".
            '[2026-01-29T14:28:38.066843+00:00] request.CRITICAL: Message 2 {"ctx":2} {"ext":2}'."\n";

        file_put_contents($tempFile, $content);

        $entries = iterator_to_array($this->parser->parse($tempFile));

        $this->assertCount(2, $entries);
        $this->assertContainsOnlyInstancesOf(LogEntry::class, $entries);
        $this->assertEquals('Message 1', $entries[0]->message);
        $this->assertEquals('Message 2', $entries[1]->message);

        unlink($tempFile);
    }

    public function testParseLine(): void
    {
        $line = '[2026-01-29T14:28:37.851112+00:00] request.INFO: Matched route "app_shared_home". {"route":"app_shared_home","route_parameters":{"_route":"app_shared_home","_controller":"App\\\\Shared\\\\Ui\\\\Controller\\\\HomeController"},"request_uri":"https://localhost/","method":"GET"} {"url":"/","ip":"172.19.0.1","http_method":"GET","server":"localhost","referrer":null}';

        $entry = $this->parser->parseLine($line);

        $this->assertInstanceOf(LogEntry::class, $entry);
        $this->assertEquals(new \DateTimeImmutable('2026-01-29T14:28:37.851112+00:00'), $entry->datetime);
        $this->assertEquals('INFO', $entry->level);
        $this->assertEquals('Matched route "app_shared_home".', $entry->message);
        $this->assertIsArray($entry->context);
        $this->assertEquals('app_shared_home', $entry->context['route']);
        $this->assertIsArray($entry->extra);
        $this->assertEquals('172.19.0.1', $entry->extra['ip']);
    }

    public function testParseLineWithInvalidFormatReturnsNull(): void
    {
        $line = 'invalid log line';

        $entry = $this->parser->parseLine($line);

        $this->assertNull($entry);
    }
}
