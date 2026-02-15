<?php

declare(strict_types=1);

namespace App\Tests\Unit\Experience\Infrastructure\State\Processor;

use App\Experience\Domain\Entity\Experience;
use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use App\Experience\Infrastructure\State\Processor\CreateExperienceProcessor;
use App\Experience\Ui\Form\Data\ExperienceDto;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final class CreateExperienceProcessorTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private ObjectMapperInterface&MockObject $objectMapper;

    private CreateExperienceProcessor $processor;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->objectMapper = $this->createMock(ObjectMapperInterface::class);

        $this->processor = new CreateExperienceProcessor(
            entityManager: $this->entityManager,
            objectMapper: $this->objectMapper,
        );
    }

    public function testItProcessData(): void
    {
        $data = new ExperienceDto(
            company: 'SensioLabs',
            type: ExperienceTypeEnum::PermanentContract,
            position: ExperiencePositionEnum::BackendDeveloper,
            startAt: new \DateTimeImmutable('2024-01-01'),
        );

        $experience = new Experience();

        $this->objectMapper
            ->expects($this->once())
            ->method('map')
            ->with($data, Experience::class)
            ->willReturn($experience)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($experience)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $result = $this->processor->process($data);

        $this->assertSame($experience, $result);
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidInstance(): void
    {
        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('persist')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process(new \stdClass());
    }

    #[DataProvider('invalidDataProvider')]
    public function testItThrowInvalidArgumentExceptionWithInvalidData(ExperienceDto $data): void
    {
        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('persist')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $this->expectException(\InvalidArgumentException::class);

        $this->processor->process($data);
    }

    /**
     * @return iterable<string, array{0: ExperienceDto}>
     */
    public static function invalidDataProvider(): iterable
    {
        yield 'missing company' => [
            new ExperienceDto(
                company: null,
                type: ExperienceTypeEnum::PermanentContract,
                position: ExperiencePositionEnum::BackendDeveloper,
                startAt: new \DateTimeImmutable(),
            ),
        ];

        yield 'missing type' => [
            new ExperienceDto(
                company: 'SensioLabs',
                type: null,
                position: ExperiencePositionEnum::BackendDeveloper,
                startAt: new \DateTimeImmutable(),
            ),
        ];

        yield 'missing position' => [
            new ExperienceDto(
                company: 'SensioLabs',
                type: ExperienceTypeEnum::PermanentContract,
                position: null,
                startAt: new \DateTimeImmutable(),
            ),
        ];

        yield 'missing startAt' => [
            new ExperienceDto(
                company: 'SensioLabs',
                type: ExperienceTypeEnum::PermanentContract,
                position: ExperiencePositionEnum::BackendDeveloper,
                startAt: null,
            ),
        ];
    }
}
