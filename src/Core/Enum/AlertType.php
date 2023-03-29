<?php

declare(strict_types=1);

namespace App\Core\Enum;

enum AlertType: string
{
    case Alert = 'alert';
    case Toast = 'toast';
}
