<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum RouteNameEnum: string
{
    case WebsiteHome = 'app_website_home';
    case WebsiteContact = 'app_website_contact';
    case WebsiteLogin = 'app_website_login';
    case WebsiteSettings = 'app_website_settings';
}
