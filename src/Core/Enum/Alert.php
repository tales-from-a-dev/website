<?php

declare(strict_types=1);

namespace App\Core\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ExtrasTrait;

enum Alert: string implements ColoreableEnumInterface
{
    use ExtrasTrait;

    #[EnumCase(extras: ['color' => 'text-green-400 border-green-800'])]
    case Success = 'success';

    #[EnumCase(extras: ['color' => 'text-blue-400 border-blue-800'])]
    case Info = 'info';

    #[EnumCase(extras: ['color' => 'text-red-400 border-red-800'])]
    case Error = 'error';

    #[EnumCase(extras: ['color' => 'text-yellow-400 border-yellow-800'])]
    case Warning = 'warning';

    public function getColor(): string
    {
        return $this->getExtra('color');
    }
}
