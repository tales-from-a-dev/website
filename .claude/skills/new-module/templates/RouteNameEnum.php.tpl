<?php

declare(strict_types=1);

namespace App\{{Module}}\Domain\Enum;

enum {{Module}}RouteNameEnum: string
{
    case WebsiteIndex = 'app_website_{{module}}_index';
    case DashboardIndex = 'app_dashboard_{{module}}_index';
    case DashboardNew = 'app_dashboard_{{module}}_new';
    case DashboardEdit = 'app_dashboard_{{module}}_edit';
}
