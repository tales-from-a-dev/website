<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Ui\Form\Data\ContactDto;

interface ContactServiceInterface
{
    public function notify(ContactDto $contact): void;
}
