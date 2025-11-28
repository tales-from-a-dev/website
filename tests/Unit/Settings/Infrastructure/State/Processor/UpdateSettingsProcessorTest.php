<?php

declare(strict_types=1);

namespace App\Tests\Unit\Settings\Infrastructure\State\Processor;

use App\Settings\Domain\Entity\Settings;
use App\Settings\Infrastructure\State\Processor\UpdateSettingsProcessor;
use App\Settings\Ui\Form\Data\SettingsDto;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final class UpdateSettingsProcessorTest extends TestCase
{
    private MockObject $entityManager;
    private MockObject $objectMapper;

    private UpdateSettingsProcessor $processor;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->objectMapper = $this->createMock(ObjectMapperInterface::class);

        $this->processor = new UpdateSettingsProcessor(
            entityManager: $this->entityManager,
            objectMapper: $this->objectMapper,
        );
    }

    public function testItProcessData(): void
    {
        $previousData = $this->createStub(Settings::class);
        $previousData->id = 1;

        $data = new SettingsDto(
            available: true,
            availableAt: new \DateTimeImmutable('tomorrow'),
            averageDailyRate: 500,
        );

        $this->objectMapper
            ->expects($this->once())
            ->method('map')
            ->with(
                $data,
                $previousData
            )
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->processor->process($data, ['previous_data' => $previousData]);
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidInstance(): void
    {
        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process(new \stdClass());
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidData(): void
    {
        $data = new SettingsDto(
            available: null,
            availableAt: new \DateTimeImmutable('tomorrow'),
            averageDailyRate: 500,
        );

        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process($data);
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidPreviousData(): void
    {
        $data = new SettingsDto(
            available: true,
            availableAt: new \DateTimeImmutable('tomorrow'),
            averageDailyRate: 500,
        );

        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process($data, ['previous_data' => new \stdClass()]);
    }
}
