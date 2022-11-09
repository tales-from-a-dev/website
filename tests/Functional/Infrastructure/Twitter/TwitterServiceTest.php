<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Twitter;

use App\Infrastructure\Twitter\TwitterService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class TwitterServiceTest extends KernelTestCase
{
    public function testItCanGetLastTweets(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        /** @var TwitterService $twitterService */
        $twitterService = $container->get(TwitterService::class);

        self::assertInstanceOf(TwitterService::class, $twitterService);

        $lastTweets = $twitterService->getTimeline();

        self::assertCount(5, $lastTweets);
        self::assertArrayHasKey('edit_history_tweet_ids', $lastTweets[0]);
        self::assertArrayHasKey('id', $lastTweets[0]);
        self::assertArrayHasKey('text', $lastTweets[0]);
    }
}
