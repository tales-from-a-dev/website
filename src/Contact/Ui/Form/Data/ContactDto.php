<?php

declare(strict_types=1);

namespace App\Contact\Ui\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $fullName = null,

        #[Assert\NotBlank(allowNull: true)]
        #[Assert\Length(min: 1, max: 255)]
        public ?string $company = null,

        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 1000)]
        public ?string $content = null,
    ) {
    }
}
