<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * @implements MetadataInterface<GitHubProject>
 */
final readonly class GitHubProject implements MetadataInterface
{
    /**
     * @param array<int, array{name: string, color: string}> $languages
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public int $forkCount,
        public int $stargazerCount,
        public string $url,
        public array $languages = [],
    ) {
    }

    /**
     * @param array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     forkCount: int,
     *     stargazerCount: int,
     *     url: string,
     *     languages: array{
     *         nodes?: array<int, array{name: string, color: string}>
     *     },
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            description: $data['description'],
            forkCount: $data['forkCount'],
            stargazerCount: $data['stargazerCount'],
            url: $data['url'],
            languages: array_map(
                static fn (array $value): array => $value,
                $data['languages']['nodes'] ?? []
            ),
        );
    }
}
