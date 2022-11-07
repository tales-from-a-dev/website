<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model;

use App\Domain\Contact\Model\Contact;
use PHPUnit\Framework\TestCase;

final class ContactTest extends TestCase
{
    public function testItCanInstantiateContact(): void
    {
        $contact = new Contact('John Doe', 'johndoe@example.com', 'Hello World');

        self::assertSame('John Doe', $contact->name);
        self::assertSame('johndoe@example.com', $contact->email);
        self::assertSame('Hello World', $contact->content);
    }
}
