<?php

declare(strict_types=1);

namespace App\Blog\Domain\Exception;

final class InvalidBlogPostException extends \InvalidArgumentException
{
    public static function invalidFilename(string $path): self
    {
        return new self(\sprintf('Blog post "%s" must be named YYYY-mm-dd-slug.md.', $path));
    }

    public static function invalidPublicationDate(string $path, string $date): self
    {
        return new self(\sprintf('Blog post "%s" has an invalid publication date "%s".', $path, $date));
    }

    public static function missingTitle(string $path): self
    {
        return new self(\sprintf('Blog post "%s" is missing a title in its front matter.', $path));
    }

    public static function missingCategory(string $path): self
    {
        return new self(\sprintf('Blog post "%s" is missing a category in its front matter.', $path));
    }

    public static function invalidCategory(string $path, string $category): self
    {
        return new self(\sprintf('Blog post "%s" has an invalid category "%s".', $path, $category));
    }
}
