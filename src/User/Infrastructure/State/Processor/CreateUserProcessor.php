<?php

declare(strict_types=1);

namespace App\User\Infrastructure\State\Processor;

use App\Shared\Domain\State\ProcessorInterface;
use App\User\Domain\Entity\User;
use App\User\Ui\Form\Data\UserDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<UserDto|null, User>
 */
final readonly class CreateUserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectMapperInterface $objectMapper,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function process(mixed $data, array $context = []): User
    {
        Assert::isInstanceOf($data, UserDto::class);
        Assert::notNull($data->email);
        Assert::notNull($data->password);

        $user = $this->objectMapper->map($data, User::class);
        $user->password = $this->passwordHasher->hashPassword($user, $data->password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
