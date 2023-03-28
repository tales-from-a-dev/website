<?php

declare(strict_types=1);

namespace App\Core\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\ExtrasTrait;

enum AlertStatus implements ColoreableEnumInterface
{
    use ExtrasTrait;

    #[EnumCase(extras: ['color' => 'text-green-800 border-green-300 bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800'])]
    case Success;

    #[EnumCase(extras: ['color' => 'text-blue-800 border-blue-300 bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800'])]
    case Info;

    #[EnumCase(extras: ['color' => 'text-red-800 border-red-300 bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800'])]
    case Error;

    #[EnumCase(extras: ['color' => 'text-yellow-800 border-yellow-300 bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800'])]
    case Warning;

    public function getColor(): string
    {
        return $this->getExtra('color', true);
    }
}
