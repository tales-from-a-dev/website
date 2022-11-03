<?php

declare(strict_types=1);

namespace App\Core\Enum;

enum AlertType: string
{
    case Success = 'success';
    case Info = 'info';
    case Error = 'error';
    case Warning = 'warning';
}
