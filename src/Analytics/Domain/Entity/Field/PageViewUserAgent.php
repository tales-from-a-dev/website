<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Entity\Field;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;
use Webmozart\Assert\Assert;

#[Orm\Embeddable]
final class PageViewUserAgent
{
    #[Orm\Column(name: 'user_agent', type: Types::STRING, length: 255)]
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::lengthBetween($value, 1, 255);

        $this->value = $value;
    }
}
