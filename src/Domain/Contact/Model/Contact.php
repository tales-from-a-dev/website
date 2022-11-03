<?php

declare(strict_types=1);

namespace App\Domain\Contact\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class Contact
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string $name = null,
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,
        #[Assert\NotBlank]
        #[Assert\Length(min: 30)]
        public ?string $content = null,
    ) {
    }
}
