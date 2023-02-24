<?php

declare(strict_types=1);

namespace App\Core\Enum;

enum Role: string
{
    case Public = 'PUBLIC_ACCESS';
    case User = 'ROLE_USER';
}
