<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\User\Domain\Enum\UserRoleEnum;
use App\User\Infrastructure\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Orm\Table(name: '`user`')]
#[Orm\Entity(repositoryClass: UserRepository::class)]
#[Orm\UniqueConstraint(columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    #[Orm\Id]
    #[Orm\GeneratedValue]
    #[Orm\Column]
    public ?int $id = null;

    #[Orm\Column(type: Types::STRING, length: 255)]
    public string $email;

    #[Orm\Column(type: Types::STRING, length: 255)]
    public string $password;

    /**
     * @var array<string>
     */
    #[Orm\Column(type: Types::JSON)]
    public array $roles = [] {
        get {
            $roles = $this->roles;
            // guarantee every user at least has ROLE_USER
            $roles[] = UserRoleEnum::User->value;

            return array_unique($roles);
        }
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        if ('' === $this->email) {
            throw new \LogicException('The user identifier cannot be empty.');
        }

        return $this->email;
    }

    /**
     * @see UserInterface
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
    }
}
