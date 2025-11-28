<?php

declare(strict_types=1);

namespace App\Shared\Domain\Enum;

use Elao\Enum\Attribute\EnumCase;
use Elao\Enum\Attribute\ReadableEnum;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumInterface;
use Elao\Enum\Bridge\Symfony\Translation\TranslatableEnumTrait;
use Elao\Enum\ExtrasTrait;

#[ReadableEnum(prefix: 'enum.alert_status.', useValueAsDefault: true)]
enum AlertStatusEnum: string implements TranslatableEnumInterface
{
    use ExtrasTrait;
    use TranslatableEnumTrait;

    #[EnumCase(
        extras: [
            'icon' => 'tabler:circle-x',
        ]
    )]
    case Error = 'error';

    #[EnumCase(
        extras: [
            'icon' => 'tabler:circle-check',
        ]
    )]
    case Success = 'success';

    public function getIcon(): string
    {
        return $this->getExtra('icon');
    }
}
