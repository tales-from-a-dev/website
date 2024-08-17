<?php

declare(strict_types=1);

namespace App\Core\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

class TalesFromADevStyle extends SymfonyStyle
{
    /**
     * @param array<string>|string $message
     */
    #[\Override]
    public function info(array|string $message): void
    {
        $this->block($message, 'INFO', 'fg=white;bg=bright-blue', ' ', true);
    }
}
