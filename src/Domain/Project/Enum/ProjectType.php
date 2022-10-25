<?php

declare(strict_types=1);

namespace App\Domain\Project\Enum;

enum ProjectType: string
{
    case Customer = 'customer';
    case GitHub = 'github';
}
