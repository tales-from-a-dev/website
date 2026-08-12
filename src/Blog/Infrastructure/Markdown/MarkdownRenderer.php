<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Markdown;

use Tempest\Markdown\Markdown;
use Tempest\Markdown\ParsedMarkdown;

final readonly class MarkdownRenderer
{
    private Markdown $markdown;

    public function __construct()
    {
        // tempest/markdown ships no bundle, so the vendor parser is built here
        // rather than registered in the container
        $this->markdown = new Markdown();
    }

    public function parse(string $raw): ParsedMarkdown
    {
        return $this->markdown->parse($raw);
    }
}
