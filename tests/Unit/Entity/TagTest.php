<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Domain\Blog\Entity\Tag;
use PHPUnit\Framework\TestCase;

final class TagTest extends TestCase
{
    public function testItCanInstantiateTag(): void
    {
        $tag = new Tag();
        $tag->setName('Dummy tag');
        $tag->setSlug();
        $tag->setCreatedAt();
        $tag->setUpdatedAt();

        self::assertSame('Dummy tag', $tag->getName());
        self::assertSame('dummy-tag', $tag->getSlug());
        self::assertEmpty($tag->getPosts());
        self::assertNotNull($tag->getCreatedAt());
        self::assertNotNull($tag->getUpdatedAt());
    }

    public function testItCanCastTagToString(): void
    {
        $tag = new Tag();
        $tag->setName('Dummy tag');

        self::assertSame('Dummy tag', (string) $tag);
    }
}
