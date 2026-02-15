<?php

declare(strict_types=1);

namespace App\Experience\Domain\Enum;

enum ExperienceRouteNameEnum: string
{
    case DashboardIndex = 'app_dashboard_experience_index';
    case DashboardNew = 'app_dashboard_experience_new';
    case DashboardEdit = 'app_dashboard_experience_edit';
}
