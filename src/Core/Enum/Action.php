<?php

declare(strict_types=1);

namespace App\Core\Enum;

enum Action: string
{
    case View = 'view';
    case Edit = 'edit';
}
