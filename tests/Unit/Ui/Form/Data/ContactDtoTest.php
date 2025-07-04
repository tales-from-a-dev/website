<?php

declare(strict_types=1);

namespace App\Tests\Unit\Ui\Form\Data;

use App\Ui\Form\Data\ContactDto;
use PHPUnit\Framework\TestCase;

final class ContactDtoTest extends TestCase
{
    public function testItCanInstantiateDto(): void
    {
        $contact = new ContactDto('John Doe', 'ACME Corp', 'johndoe@example.com', 'Hello World');

        self::assertSame('John Doe', $contact->fullName);
        self::assertSame('ACME Corp', $contact->company);
        self::assertSame('johndoe@example.com', $contact->email);
        self::assertSame('Hello World', $contact->content);
    }
}
