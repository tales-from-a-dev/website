<?php

declare(strict_types=1);

namespace App\User\Domain\Enum;

enum UserRouteNameEnum: string
{
    case DashboardLogin = 'app_dashboard_user_login';
}
