<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum RouteNameEnum: string
{
    // website
    case WebsiteHome = 'app_website_home';
    case WebsiteContactIndex = 'app_website_contact_index';
}
