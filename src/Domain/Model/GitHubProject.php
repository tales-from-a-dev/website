<?php

declare(strict_types=1);

namespace App\Domain\Model;

/**
 * @implements MetadataInterface<GitHubProject>
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
