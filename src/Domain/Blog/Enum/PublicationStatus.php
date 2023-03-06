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

    #[EnumCase(label: 'enum.publication_status.draft', extras: ['color' => 'bg-yellow-900 text-yellow-300'])]
    case Draft = 'draft';

    #[EnumCase(label: 'enum.publication_status.frozen', extras: ['color' => 'bg-blue-900 text-blue-300'])]
    case Frozen = 'frozen';

    #[EnumCase(label: 'enum.publication_status.published', extras: ['color' => 'bg-green-900 text-green-300'])]
    case Published = 'published';

    public function getColor(): string
    {
        return $this->getExtra('color');
    }
}
