<?php

declare(strict_types=1);

namespace App\Domain\Project\Model;

/**
 * @implements \App\Domain\Project\Model\MetadataInterface<\App\Domain\Project\Model\GitHubProject>
 */
final class GitHubProject implements MetadataInterface
{
    /**
     * @param array<string> $languages
     */
    public function __construct(
        public readonly string $id,
        public readonly int $forkCount,
        public readonly int $stargazerCount,
        public readonly array $languages = [],
    ) {
    }
}
