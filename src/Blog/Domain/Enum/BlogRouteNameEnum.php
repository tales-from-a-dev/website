<?php

declare(strict_types=1);

namespace App\Blog\Domain\Enum;

enum BlogRouteNameEnum: string
{
    // website
    case WebsiteIndex = 'app_website_blog_index';
    case WebsiteShow = 'app_website_blog_show';
    case WebsiteCategory = 'app_website_blog_category';
    case WebsiteTag = 'app_website_blog_tag';
}
