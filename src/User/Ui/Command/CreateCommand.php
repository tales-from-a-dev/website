<?php

declare(strict_types=1);

namespace App\User\Ui\Command;

use App\User\Infrastructure\State\Processor\CreateUserProcessor;
use App\User\Ui\Form\Data\UserDto;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Creates users and stores them in the database'
)]
final readonly class CreateCommand
{
    public function __construct(
        private ValidatorInterface $validator,
        private CreateUserProcessor $processor,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'The email of the new user')] string $email,
        #[Argument(description: 'The plain password of the new user')] string $password,
    ): int {
        $dto = new UserDto($email, $password);

        if (\count($errors = $this->validator->validate($dto)) > 0) {
            $errorsAsString = (string) $errors;

            $io->error($errorsAsString);

            return Command::FAILURE;
        }

        $user = $this->processor->process($dto);

        $io->success(\sprintf('User "%s" created successfully!', $user->email));

        return Command::SUCCESS;
    }
}
