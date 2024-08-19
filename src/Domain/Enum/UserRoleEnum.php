<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum UserRoleEnum: string
{
    case User = 'ROLE_USER';

    case Public = 'PUBLIC_ACCESS';
    case AllowedSwitch = 'ROLE_ALLOWED_TO_SWITCH';

    case IsAuth = 'IS_AUTHENTICATED';
    case IsAuthRemembered = 'IS_AUTHENTICATED_REMEMBERED';
    case IsAuthFully = 'IS_AUTHENTICATED_FULLY';

    case IsRemembered = 'IS_REMEMBERED';
    case IsImpersonator = 'IS_IMPERSONATOR';
}
