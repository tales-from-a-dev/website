<?php

declare(strict_types=1);

namespace App\Core\Model;

use App\Core\Enum\AlertType;
use Symfony\Component\Translation\TranslatableMessage;

final class Alert implements \Stringable
{
    public function __construct(
        public readonly AlertType $type,
        public readonly TranslatableMessage $message
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->message;
    }
}
