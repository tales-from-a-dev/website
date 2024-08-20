<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class Contact
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $name = null,

        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 1000)]
        public ?string $content = null,
    ) {
    }
}
