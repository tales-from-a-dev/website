<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Entity;

use App\Analytics\Domain\Entity\Field\PageViewId;
use App\Analytics\Domain\Entity\Field\PageViewIp;
use App\Analytics\Domain\Entity\Field\PageViewMethod;
use App\Analytics\Domain\Entity\Field\PageViewReferer;
use App\Analytics\Domain\Entity\Field\PageViewServer;
use App\Analytics\Domain\Entity\Field\PageViewUrl;
use App\Analytics\Domain\Entity\Field\PageViewUserAgent;
use App\Analytics\Domain\Entity\Field\PageViewVisitedAt;
use App\Shared\Domain\Entity\CreatedAt;
use Doctrine\ORM\Mapping as Orm;

#[Orm\Entity]
#[Orm\Index(name: 'url_idx', columns: ['url'])]
#[Orm\Index(name: 'visited_at_idx', columns: ['visited_at'])]
#[Orm\Index(name: 'created_at_idx', columns: ['created_at'])]
class PageView
{
    #[Orm\Embedded(columnPrefix: false)]
    public PageViewId $id;

    #[Orm\Embedded(columnPrefix: false)]
    public CreatedAt $createdAt;

    public function __construct(
        #[Orm\Embedded(columnPrefix: false)]
        public PageViewUrl $url,

        #[Orm\Embedded(columnPrefix: false)]
        public PageViewMethod $method,

        #[Orm\Embedded(columnPrefix: false)]
        public PageViewServer $server,

        #[Orm\Embedded(columnPrefix: false)]
        public PageViewIp $ip,

        #[Orm\Embedded(columnPrefix: false)]
        public PageViewUserAgent $userAgent,

        #[Orm\Embedded(columnPrefix: false)]
        public PageViewReferer $referer,

        #[Orm\Embedded(columnPrefix: false)]
        public PageViewVisitedAt $visitedAt,
    ) {
        $this->id = new PageViewId();
        $this->createdAt = new CreatedAt();
    }
}
