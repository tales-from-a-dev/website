<?php

declare(strict_types=1);

namespace App\Core\Model;

use App\Core\Enum\AlertStatus;
use App\Core\Enum\AlertType;
use Symfony\Component\Translation\TranslatableMessage;

final readonly class Alert implements \Stringable
{
    public function __construct(
        public TranslatableMessage $message,
        public AlertStatus $status = AlertStatus::Success,
        public AlertType $type = AlertType::Alert
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->message;
    }
}
