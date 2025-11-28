<?php

declare(strict_types=1);

namespace App\User\Domain\Enum;

enum UserRouteNameEnum: string
{
    case WebsiteLogin = 'app_user_login';
}
