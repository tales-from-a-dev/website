<?php

declare(strict_types=1);

namespace App\Tests\Unit\Contact\Infrastructure\State\Processor;

use App\Contact\Domain\Service\ContactServiceInterface;
use App\Contact\Infrastructure\State\Processor\SendContactProcessor;
use App\Contact\Ui\Form\Data\ContactDto;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class SendContactProcessorTest extends TestCase
{
    private MockObject $contactService;
    private MockObject $logger;

    private SendContactProcessor $processor;

    protected function setUp(): void
    {
        $this->contactService = $this->createMock(ContactServiceInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->processor = new SendContactProcessor(
            contactService: $this->contactService,
            logger: $this->logger,
        );
    }

    public function testItProcessData(): void
    {
        $data = new ContactDto(
            fullName: 'John Doe',
            company: 'ACME Corp',
            email: 'johndoe@example.com',
            content: 'Hello World',
        );

        $this->contactService
            ->expects($this->once())
            ->method('notify')
            ->with($data)
        ;

        $this->logger
            ->expects($this->never())
            ->method('error')
        ;

        $this->assertTrue($this->processor->process($data));
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidInstance(): void
    {
        $this->contactService
            ->expects($this->never())
            ->method('notify')
        ;

        $this->logger
            ->expects($this->never())
            ->method('error')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process(new \stdClass());
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidData(): void
    {
        $data = new ContactDto(
            fullName: null,
            email: 'johndoe@example.com',
            content: 'Hello World',
        );

        $this->contactService
            ->expects($this->never())
            ->method('notify')
        ;

        $this->logger
            ->expects($this->never())
            ->method('error')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process($data);
    }

    public function testItThrowExceptionWithServiceUnavailable(): void
    {
        $data = new ContactDto(
            fullName: 'John Doe',
            company: 'ACME Corp',
            email: 'johndoe@example.com',
            content: 'Hello World',
        );

        $exception = new \Exception('Error');

        $this->contactService
            ->expects($this->once())
            ->method('notify')
            ->with($data)
            ->willThrowException($exception)
        ;

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($exception->getMessage())
        ;

        $this->assertFalse($this->processor->process($data));
    }
}
