<?php

declare(strict_types=1);

namespace App\Domain\Project\Model;

/**
 * @template T
 *
 * @implements MetadataInterface<T>
 */
final readonly class GitHubProject implements MetadataInterface
{
    /**
     * @param array<string> $languages
     */
    public function __construct(
        public string $id,
        public int $forkCount,
        public int $stargazerCount,
        public array $languages = [],
    ) {
    }
}
