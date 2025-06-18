<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Dto\ContactDto;

interface ContactServiceInterface
{
    public function notify(ContactDto $contact): void;
}
