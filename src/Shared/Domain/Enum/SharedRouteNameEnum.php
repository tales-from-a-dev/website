<?php

declare(strict_types=1);

namespace App\Shared\Domain\Enum;

enum SharedRouteNameEnum: string
{
    // website
    case WebsiteIndex = 'app_website_shared_index';

    // dashboard
    case DashboardIndex = 'app_dashboard_shared_index';
}
