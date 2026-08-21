<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Service;

use App\Blog\Domain\Service\ReadingTimeCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ReadingTimeCalculatorTest extends TestCase
{
    private ReadingTimeCalculator $readingTimeCalculator;

    protected function setUp(): void
    {
        $this->readingTimeCalculator = new ReadingTimeCalculator();
    }

    #[DataProvider('provideWordCounts')]
    public function testItRoundsUpToTheNextMinute(int $words, int $expected): void
    {
        $html = \sprintf('<p>%s</p>', implode(' ', array_fill(0, $words, 'word')));

        self::assertSame($expected, $this->readingTimeCalculator->minutes($html));
    }

    #[DataProvider('provideEmptyContent')]
    public function testItNeverReturnsLessThanAMinute(string $html): void
    {
        self::assertSame(1, $this->readingTimeCalculator->minutes($html));
    }

    public function testItCountsTextRatherThanMarkup(): void
    {
        // 200 text words wrapped in tags and attributes that must not be counted
        $html = str_repeat('<h2 id="a-heading" class="font-medium">one two</h2><p><a href="/blog">three</a> four</p>', 50);

        self::assertSame(1, $this->readingTimeCalculator->minutes($html));
    }

    public function testItDecodesEntitiesBeforeCounting(): void
    {
        // `&nbsp;` would otherwise glue its neighbours into a single word
        $glued = \sprintf('<p>%s</p>', str_repeat('one&nbsp;two ', 150));

        self::assertSame(2, $this->readingTimeCalculator->minutes($glued));
    }

    public function testItCountsAccentedWords(): void
    {
        $html = \sprintf('<p>%s</p>', implode(' ', array_fill(0, 201, 'développeur')));

        self::assertSame(2, $this->readingTimeCalculator->minutes($html));
    }

    public static function provideWordCounts(): iterable
    {
        yield 'one word' => [1, 1];
        yield 'exactly one minute' => [200, 1];
        yield 'one word over' => [201, 2];
        yield 'exactly two minutes' => [400, 2];
        yield 'six minutes' => [1_101, 6];
    }

    public static function provideEmptyContent(): iterable
    {
        yield 'empty string' => [''];
        yield 'whitespace only' => ["  \n\t "];
        yield 'markup without text' => ['<p></p><hr />'];
    }
}
