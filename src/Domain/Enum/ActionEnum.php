<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum ActionEnum: string
{
    case View = 'view';
    case Edit = 'edit';
}
