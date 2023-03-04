<?php

declare(strict_types=1);

namespace App\Domain\Blog\Enum;

use App\Core\Enum\ColoreableEnumInterface;
use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum PublicationStatus: string implements ReadableEnumInterface, ColoreableEnumInterface
{
    use ExtrasTrait;
    use ReadableEnumTrait;

    #[EnumCase(label: 'enum.publication_status.draft', extras: ['color' => 'badge-warning'])]
    case Draft = 'draft';

    #[EnumCase(label: 'enum.publication_status.frozen', extras: ['color' => 'badge-secondary'])]
    case Frozen = 'frozen';

    #[EnumCase(label: 'enum.publication_status.published', extras: ['color' => 'badge-primary'])]
    case Published = 'published';

    public function getColor(): string
    {
        return $this->getExtra('color');
    }
}
