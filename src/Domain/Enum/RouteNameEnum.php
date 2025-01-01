<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum RouteNameEnum: string
{
    // website
    case WebsiteHome = 'app_website_home';
    case WebsiteLogin = 'app_website_login';
    case WebsiteSettings = 'app_website_settings';
}
