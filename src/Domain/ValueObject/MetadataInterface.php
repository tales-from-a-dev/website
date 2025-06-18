<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * @template T
 */
#[DiscriminatorMap(typeProperty: 'type', mapping: [
    'github_project' => GitHubProject::class,
])]
interface MetadataInterface
{
}
