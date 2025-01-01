<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumInterface;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumTrait;

#[ReadableEnum(prefix: 'enum.alert_status.', useValueAsDefault: true)]
enum AlertStatusEnum: string implements TranslatableEnumInterface
{
    use TranslatableEnumTrait;

    case Danger = 'danger';
    case Success = 'success';
}
