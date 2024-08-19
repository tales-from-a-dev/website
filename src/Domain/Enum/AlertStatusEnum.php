<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\ReadableEnumInterface;
use Elao\Enum\ReadableEnumTrait;

#[ReadableEnum(prefix: 'enum.alert_status.', useValueAsDefault: true)]
enum AlertStatusEnum: string implements ReadableEnumInterface
{
    use ExtrasTrait;
    use ReadableEnumTrait;

    case Default = 'default';
    case Danger = 'danger';
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
}
