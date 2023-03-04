<?php

declare(strict_types=1);

namespace App\Domain\Project\Enum;

use App\Core\Enum\ColoreableEnumInterface;
use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

enum ProjectType: string implements ReadableEnumInterface, ColoreableEnumInterface
{
    use ExtrasTrait;
    use ReadableEnumTrait;

    #[EnumCase(label: 'enum.project_type.customer', extras: ['color' => 'bg-indigo-900 text-indigo-400'])]
    case Customer = 'customer';

    #[EnumCase(label: 'enum.project_type.github', extras: ['color' => 'bg-purple-900 text-purple-400'])]
    case GitHub = 'github';

    public function getColor(): string
    {
        return $this->getExtra('color');
    }
}
