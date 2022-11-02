<?php

declare(strict_types=1);

namespace App\Domain\Blog\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum PublicationStatus: string implements ReadableEnumInterface
{
    use ReadableEnumTrait;

    #[EnumCase(label: 'enum.publication_status.draft')]
    case Draft = 'draft';

    #[EnumCase(label: 'enum.publication_status.frozen')]
    case Frozen = 'frozen';

    #[EnumCase(label: 'enum.publication_status.published')]
    case Published = 'published';
}
