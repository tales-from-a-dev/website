<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Clock\Clock;

#[Orm\Entity]
#[Orm\Index(name: 'url_idx', columns: ['url'])]
#[Orm\Index(name: 'visited_at_idx', columns: ['visited_at'])]
#[Orm\Index(name: 'created_at_idx', columns: ['created_at'])]
class PageView
{
    #[Orm\Id]
    #[Orm\GeneratedValue]
    #[Orm\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    public ?int $id = null;

    #[Orm\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;

    public function __construct(
        #[Orm\Column(type: Types::STRING, length: 255)]
        public string $url,

        #[Orm\Column(type: Types::STRING, length: 10)]
        public string $method,

        #[Orm\Column(type: Types::STRING, length: 255)]
        public string $server,

        #[Orm\Column(type: Types::STRING, length: 255)]
        public string $ip,

        #[Orm\Column(type: Types::STRING, length: 255)]
        public string $userAgent,

        #[Orm\Column(type: Types::STRING, length: 255, nullable: true)]
        public ?string $referer,

        #[Orm\Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $visitedAt,
    ) {
        $this->createdAt = Clock::get()->now();
    }
}
