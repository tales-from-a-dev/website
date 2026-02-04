<?php

declare(strict_types=1);

namespace App\Analytics\Domain\Entity\Field;

use App\Shared\Domain\Entity\AggregateRootId;
use Doctrine\ORM\Mapping as Orm;

#[Orm\Embeddable]
final class PageViewId
{
    use AggregateRootId;
}
