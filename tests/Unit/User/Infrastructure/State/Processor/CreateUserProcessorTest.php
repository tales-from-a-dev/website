<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Infrastructure\State\Processor;

use App\User\Domain\Entity\User;
use App\User\Infrastructure\State\Processor\CreateUserProcessor;
use App\User\Ui\Form\Data\UserDto;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserProcessorTest extends TestCase
{
    private MockObject $entityManager;
    private MockObject $objectMapper;
    private MockObject $passwordHasher;

    private CreateUserProcessor $processor;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->objectMapper = $this->createMock(ObjectMapperInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->processor = new CreateUserProcessor(
            entityManager: $this->entityManager,
            objectMapper: $this->objectMapper,
            passwordHasher: $this->passwordHasher,
        );
    }

    public function testItProcessData(): void
    {
        $hashedPassword = '$2y$13$wwgbz4O8Sl1cx1NoUYA3aOKwPGboT9nh.qpnNKclec64.QlKlDRXO';

        $user = new User();
        $user->email = 'john.doe@example.com';

        $data = new UserDto(
            email: 'john.doe@example.com',
            password: 'password',
        );

        $this->objectMapper
            ->expects($this->once())
            ->method('map')
            ->with(
                $data,
                $user::class,
            )
            ->willReturn($user)
        ;

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, $data->password)
            ->willReturn($hashedPassword)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $user = $this->processor->process($data);

        $this->assertSame('john.doe@example.com', $user->email);
        $this->assertSame($hashedPassword, $user->password);
    }

    public function testItThrowInvalidArgumentExceptionWithInvalidInstance(): void
    {
        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->passwordHasher
            ->expects($this->never())
            ->method('hashPassword')
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

    public function testItThrowInvalidArgumentExceptionWithInvalidData(): void
    {
        $data = new UserDto();

        $this->objectMapper
            ->expects($this->never())
            ->method('map')
        ;

        $this->passwordHasher
            ->expects($this->never())
            ->method('hashPassword')
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
}
