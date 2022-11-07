<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Enum\PublicationStatus;
use PHPUnit\Framework\TestCase;

final class PostTest extends TestCase
{
    public function testItCanInstantiatePost(): void
    {
        $post = new Post();
        $post->setTitle('Dummy post');
        $post->setContent('Dummy post content');
        $post->setPublicationStatus(PublicationStatus::Published);
        $post->setPublishedAt($publishedAt = new \DateTimeImmutable());
        $post->setSlug();
        $post->setCreatedAt();
        $post->setUpdatedAt();

        self::assertSame('Dummy post', $post->getTitle());
        self::assertSame('Dummy post content', $post->getContent());
        self::assertSame(PublicationStatus::Published, $post->getPublicationStatus());
        self::assertSame($publishedAt, $post->getPublishedAt());
        self::assertSame('dummy-post', $post->getSlug());
        self::assertNotNull($post->getCreatedAt());
        self::assertNotNull($post->getUpdatedAt());
    }

    public function testItCanCastPostToString(): void
    {
        $post = new Post();
        $post->setTitle('Dummy post');

        self::assertSame('Dummy post', (string) $post);
    }

    public function testItCanAddAndRemoveTags(): void
    {
        $post = new Post();
        $post->addTag($tag1 = (new Tag())->setName('Tag 1'));
        $post->addTag($tag2 = (new Tag())->setName('Tag 2'));

        self::assertCount(2, $post->getTags());
        self::assertSame($tag1, $post->getTags()->first());
        self::assertSame($tag2, $post->getTags()->last());

        $post->removeTag($tag1);

        self::assertCount(1, $post->getTags());
        self::assertSame($tag2, $post->getTags()->first());
    }
}
