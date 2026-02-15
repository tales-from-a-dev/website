<?php

declare(strict_types=1);

namespace App\Tests\Unit\Experience\Infrastructure\State\Processor;

use App\Experience\Domain\Entity\Experience;
use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use App\Experience\Infrastructure\State\Processor\UpdateExperienceProcessor;
use App\Experience\Ui\Form\Data\ExperienceDto;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final class UpdateExperienceProcessorTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private ObjectMapperInterface&MockObject $objectMapper;

    private UpdateExperienceProcessor $processor;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->objectMapper = $this->createMock(ObjectMapperInterface::class);

        $this->processor = new UpdateExperienceProcessor(
            entityManager: $this->entityManager,
            objectMapper: $this->objectMapper,
        );
    }

    public function testItProcessData(): void
    {
        $previousData = $this->createStub(Experience::class);
        $previousData->id = 1;

        $data = new ExperienceDto(
            company: 'SensioLabs',
            type: ExperienceTypeEnum::PermanentContract,
            position: ExperiencePositionEnum::BackendDeveloper,
            startAt: new \DateTimeImmutable('2020-01-01'),
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

        $result = $this->processor->process($data, ['previous_data' => $previousData]);

        $this->assertSame($previousData, $result);
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidInstance(): void
    {
        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process(new \stdClass());
    }

    #[DataProvider('provideInvalidData')]
    public function testItThrowInvalidArgumentExceptionWithInvalidData(ExperienceDto $data): void
    {
        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process($data);
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidPreviousData(): void
    {
        $data = new ExperienceDto(
            company: 'SensioLabs',
            type: ExperienceTypeEnum::PermanentContract,
            position: ExperiencePositionEnum::BackendDeveloper,
            startAt: new \DateTimeImmutable(),
        );

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process($data, ['previous_data' => new \stdClass()]);
    }

    public function testItThrowInvalidArgumentExceptionWithMissingPreviousDataId(): void
    {
        $data = new ExperienceDto(
            company: 'SensioLabs',
            type: ExperienceTypeEnum::PermanentContract,
            position: ExperiencePositionEnum::BackendDeveloper,
            startAt: new \DateTimeImmutable(),
        );

        $previousData = new Experience();

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process($data, ['previous_data' => $previousData]);
    }

    /**
     * @return iterable<string, array{0: ExperienceDto}>
     */
    public static function provideInvalidData(): iterable
    {
        yield 'missing company' => [new ExperienceDto(
            company: null,
            type: ExperienceTypeEnum::PermanentContract,
            position: ExperiencePositionEnum::BackendDeveloper,
            startAt: new \DateTimeImmutable(),
        )];

        yield 'missing type' => [new ExperienceDto(
            company: 'SensioLabs',
            type: null,
            position: ExperiencePositionEnum::BackendDeveloper,
            startAt: new \DateTimeImmutable(),
        )];

        yield 'missing position' => [new ExperienceDto(
            company: 'SensioLabs',
            type: ExperienceTypeEnum::PermanentContract,
            position: null,
            startAt: new \DateTimeImmutable(),
        )];

        yield 'missing startAt' => [new ExperienceDto(
            company: 'SensioLabs',
            type: ExperienceTypeEnum::PermanentContract,
            position: ExperiencePositionEnum::BackendDeveloper,
            startAt: null,
        )];
    }
}
