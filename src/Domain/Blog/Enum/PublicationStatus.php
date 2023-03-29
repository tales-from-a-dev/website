<?php

declare(strict_types=1);

namespace App\Domain\Blog\Enum;

use App\Core\Enum\ColoreableEnumInterface;
use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

#[ReadableEnum(prefix: 'enum.publication_status.', useValueAsDefault: true)]
enum PublicationStatus: string implements ReadableEnumInterface, ColoreableEnumInterface
{
    use ExtrasTrait;
    use ReadableEnumTrait;

    #[EnumCase(extras: ['color' => 'bg-yellow-900 text-yellow-300'])]
    case Draft = 'draft';

    #[EnumCase(extras: ['color' => 'bg-blue-900 text-blue-300'])]
    case Frozen = 'frozen';

    #[EnumCase(extras: ['color' => 'bg-green-900 text-green-300'])]
    case Published = 'published';

    public function getColor(): string
    {
        return $this->getExtra('color');
    }
}
