<?php

declare(strict_types=1);

namespace App\Analytics\Ui\Command;

use App\Analytics\Application\Message\ProcessDailyTrafficMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:analytics:process_daily_traffic',
    description: 'Parse daily traffic from traffic log files'
)]
final class ProcessDailyTrafficCommand
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $io->title('Processing daily traffic');

        $error = false;
        try {
            $result = $this->handle(new ProcessDailyTrafficMessage());

            if ([] === $result) {
                $io->text('No files to process.');
            } else {
                foreach ($result as $fileName) {
                    $io->text(\sprintf('    File <info>"%s"</info> processed successfully!', $fileName));
                }
            }

            $io->success('Daily traffic processed successfully!');
        } catch (\Throwable $exception) {
            $io->error([
                'Could not process daily traffic',
                $exception->getMessage(),
            ]);

            $error = true;
        } finally {
            return $error ? Command::FAILURE : Command::SUCCESS;
        }
    }
}
