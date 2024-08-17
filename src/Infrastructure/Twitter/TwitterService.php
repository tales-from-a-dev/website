<?php

declare(strict_types=1);

namespace App\Infrastructure\Twitter;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsAlias(id: 'app.twitter', public: true)]
final readonly class TwitterService
{
    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $twitterClient,
        private LoggerInterface $logger,
        private int $twitterUserId,
    ) {
    }

    /**
     * @return array<int, mixed>
     */
    public function getTimeline(): array
    {
        try {
            return $this->cache->get('twitter_timeline', function (ItemInterface $item) {
                $item->expiresAfter(60 * 60);

                $response = $this->twitterClient->request(Request::METHOD_GET, \sprintf('/2/users/%d/tweets', $this->twitterUserId), [
                    'query' => [
                        'max_results' => 5,
                    ],
                ]);

                $json = json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

                return $json['data'] ?? [];
            });
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return [];
        }
    }
}
