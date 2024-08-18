<?php

declare(strict_types=1);

namespace App\Ui\Command;

use App\Core\Console\Style\TalesFromADevStyle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected TalesFromADevStyle $io;

    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new TalesFromADevStyle($input, $output);
    }
}
