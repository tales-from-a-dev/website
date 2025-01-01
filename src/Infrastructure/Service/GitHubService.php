<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Service\GitHubServiceInterface;
use App\Domain\ValueObject\GitHubProject;
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

    public function getPinnedRepositories(): array
    {
        try {
            return $this->cache->get('github_pinned_repositories', function (ItemInterface $item): array {
                $item->expiresAfter(60 * 60);

                $response = $this->githubClient->request(Request::METHOD_POST, '/graphql', [
                    'json' => [
                        'query' => $this->getPinnedRepositoriesQuery(),
                        'variables' => json_encode(['login' => $this->githubUsername], \JSON_THROW_ON_ERROR),
                    ],
                ]);

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
                 *                         nodes: array<int, array{name: string, color: string}
                 *                     },
                 *                 }>|null
                 *             }
                 *         }
                 *     }
                 * } $json
                 */
                $json = json_decode($response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

                return array_map(
                    static fn (array $value): GitHubProject => GitHubProject::fromArray($value),
                    $json['data']['user']['pinnedItems']['nodes']
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
