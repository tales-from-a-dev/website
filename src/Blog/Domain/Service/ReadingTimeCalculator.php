<?php

declare(strict_types=1);

namespace App\Blog\Domain\Service;

final readonly class ReadingTimeCalculator
{
    private const int WORDS_PER_MINUTE = 200;

    /**
     * Code blocks are counted like prose: reading a listing is not faster than
     * reading a paragraph, so special casing `<pre>` buys nothing.
     */
    public function minutes(string $html): int
    {
        $text = html_entity_decode(strip_tags($html), \ENT_QUOTES | \ENT_HTML5, 'UTF-8');

        // `str_word_count()` is locale dependent and mangles the accented French catalogue
        $words = \count(preg_split('/\s+/u', trim($text), -1, \PREG_SPLIT_NO_EMPTY) ?: []);

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }
}
