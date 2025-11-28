<?php

declare(strict_types=1);

namespace App\User\Ui\Form\Data;

use App\User\Domain\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints as OrmAssert;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: User::class)]
#[OrmAssert\UniqueEntity(
    fields: 'email',
    entityClass: User::class,
)]
final class UserDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 8, max: PasswordHasherInterface::MAX_PASSWORD_LENGTH, )]
        #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_MEDIUM)]
        #[Assert\NotCompromisedPassword]
        public ?string $password = null,
    ) {
    }
}
