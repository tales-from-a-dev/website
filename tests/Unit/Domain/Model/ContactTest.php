<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Dto\ContactDto;
use PHPUnit\Framework\TestCase;

final class ContactTest extends TestCase
{
    public function testItCanInstantiateContact(): void
    {
        $contact = new ContactDto('John Doe', 'johndoe@example.com', 'Hello World');

        self::assertSame('John Doe', $contact->fullName);
        self::assertSame('johndoe@example.com', $contact->email);
        self::assertSame('Hello World', $contact->content);
    }
}
