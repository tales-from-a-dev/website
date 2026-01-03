<?php

declare(strict_types=1);

namespace App\GitHub\Infrastructure\Service;

use App\GitHub\Domain\Service\GitHubServiceInterface;
use App\GitHub\Domain\ValueObject\GitHubProject;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsAlias(id: 'app.github', public: true)]
final readonly class GitHubService implements GitHubServiceInterface
{
    public function __construct(
        private CacheInterface $cache,
        private HttpClientInterface $githubClient,
        private LoggerInterface $logger,
        private string $githubUsername,
    ) {
    }

    /**
     * @return GitHubProject[]
     */
    public function getPinnedRepositories(): array
    {
        try {
            // Cannot use caching HttpClient because it does not support POST requests
            return $this->cache->get('github_pinned_repositories', function (ItemInterface $item): array {
                $item->expiresAfter(60 * 60);

                /** @var array{
                 *     data: array{
                 *         user: array{
                 *             pinnedItems: array{
                 *                 nodes: array<int, array{
                 *                     id: string,
                 *                     name: string,
                 *                     description: string,
                 *                     forkCount: int,
                 *                     stargazerCount: int,
                 *                     url: string,
                 *                     languages: array{
                 *                         nodes: array<int, array{name: string, color: string}>
                 *                     },
                 *                 }>|null
                 *             }
                 *         }
                 *     }
                 * } $data
                 */
                $data = $this->githubClient
                    ->request(Request::METHOD_POST, '/graphql', [
                        'json' => [
                            'query' => $this->getPinnedRepositoriesQuery(),
                            'variables' => json_encode(['login' => $this->githubUsername], \JSON_THROW_ON_ERROR),
                        ],
                    ])
                    ->toArray()
                ;

                return array_map(
                    static fn (array $value): GitHubProject => GitHubProject::fromArray($value),
                    $data['data']['user']['pinnedItems']['nodes']
                );
            });
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());

            return [];
        }
    }

    private function getPinnedRepositoriesQuery(): string
    {
        return <<<'QUERY'
            query getPinnedRepositories (
                $login: String!
            ) {
                user (
                    login: $login
                ) {
                    pinnedItems(
                        first: 6,
                        types: [REPOSITORY]
                    ) {
                        nodes {
                            ... on Repository {
                                id
                                name
                                description
                                forkCount
                                stargazerCount
                                url
                                languages(
                                    first: 10
                                ) {
                                    nodes {
                                        name
                                        color
                                    }
                                }
                            }
                        }
                    }
                }
            }
        QUERY;
    }
}
