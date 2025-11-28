<?php

declare(strict_types=1);

namespace App\Contact\Domain\Service;

use App\Contact\Ui\Form\Data\ContactDto;

interface ContactServiceInterface
{
    public function notify(ContactDto $contact): void;
}
