<?php

declare(strict_types=1);

namespace App\Blog\Domain\Exception;

final class UnreadableBlogPostException extends \RuntimeException
{
    public static function atPath(string $path): self
    {
        return new self(\sprintf('Blog post file "%s" is missing or could not be read.', $path));
    }
}
