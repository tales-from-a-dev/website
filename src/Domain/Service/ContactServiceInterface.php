<?php

namespace App\Domain\Service;

use App\Domain\Dto\ContactDto;

interface ContactServiceInterface
{
    public function notify(ContactDto $contact): void;
}
