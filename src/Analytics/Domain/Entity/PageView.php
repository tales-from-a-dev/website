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
    private const int URL_MAX_LENGTH = 255;
    private const int METHOD_MAX_LENGTH = 10;
    private const int SERVER_MAX_LENGTH = 255;
    private const int IP_MAX_LENGTH = 255;
    private const int USER_AGENT_MAX_LENGTH = 255;
    private const int REFERER_MAX_LENGTH = 255;

    #[Orm\Id]
    #[Orm\GeneratedValue]
    #[Orm\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    public ?int $id = null;

    #[Orm\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;

    public function __construct(
        #[Orm\Column(type: Types::STRING, length: self::URL_MAX_LENGTH)]
        public string $url,

        #[Orm\Column(type: Types::STRING, length: self::METHOD_MAX_LENGTH)]
        public string $method,

        #[Orm\Column(type: Types::STRING, length: self::SERVER_MAX_LENGTH)]
        public string $server,

        #[Orm\Column(type: Types::STRING, length: self::IP_MAX_LENGTH)]
        public string $ip,

        #[Orm\Column(type: Types::STRING, length: self::USER_AGENT_MAX_LENGTH)]
        public string $userAgent,

        #[Orm\Column(type: Types::STRING, length: self::REFERER_MAX_LENGTH, nullable: true)]
        public ?string $referer,

        #[Orm\Column(type: Types::DATETIME_IMMUTABLE)]
        public \DateTimeImmutable $visitedAt,
    ) {
        // Every string below comes from an untrusted request: an over-long URL,
        // user agent or referer must cost us the extra characters, not the page view.
        $this->url = self::truncate($url, self::URL_MAX_LENGTH);
        $this->method = self::truncate($method, self::METHOD_MAX_LENGTH);
        $this->server = self::truncate($server, self::SERVER_MAX_LENGTH);
        $this->ip = self::truncate($ip, self::IP_MAX_LENGTH);
        $this->userAgent = self::truncate($userAgent, self::USER_AGENT_MAX_LENGTH);
        $this->referer = null === $referer ? null : self::truncate($referer, self::REFERER_MAX_LENGTH);

        $this->createdAt = Clock::get()->now();
    }

    /**
     * Cuts on code points, the unit PostgreSQL counts for `character varying`.
     */
    private static function truncate(string $value, int $length): string
    {
        return mb_substr($value, 0, $length, 'UTF-8');
    }
}
