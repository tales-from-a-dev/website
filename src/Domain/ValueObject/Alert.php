<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Enum\AlertStatusEnum;
use Symfony\Component\Translation\TranslatableMessage;

final readonly class Alert implements \Stringable
{
    public function __construct(
        public TranslatableMessage $message,
        public AlertStatusEnum $status = AlertStatusEnum::Success,
    ) {
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->message;
    }
}
