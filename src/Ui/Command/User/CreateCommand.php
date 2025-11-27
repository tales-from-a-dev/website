<?php

declare(strict_types=1);

namespace App\Ui\Command\User;

use App\Infrastructure\State\Processor\CreateUserProcessor;
use App\Ui\Form\Data\UserDto;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Creates users and stores them in the database'
)]
final class CreateCommand extends Command
{
    private SymfonyStyle $ui;

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CreateUserProcessor $processor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The plain password of the new user')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->ui = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $dto = new UserDto($email, $password);

        if (\count($errors = $this->validator->validate($dto)) > 0) {
            $errorsAsString = (string) $errors;

            $this->ui->error($errorsAsString);

            return Command::FAILURE;
        }

        $user = $this->processor->process($dto);

        $this->ui->success(\sprintf('User "%s" created successfully!', $user->email));

        return Command::SUCCESS;
    }
}
