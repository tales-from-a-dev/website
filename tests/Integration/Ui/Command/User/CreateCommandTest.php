<?php

declare(strict_types=1);

namespace App\Tests\Integration\Ui\Command\User;

use App\Test\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateCommandTest extends KernelTestCase
{
    public function testItSuccessfullyExecuteCommand(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'user@example.com',
            'password' => 'Lbn967La@KS=Xt2',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('User "user@example.com" created successfully!', $output);

        UserFactory::assert()->count(1, ['email' => 'user@example.com']);
    }

    public function testItFailedExecuteCommandWithWeakPassword(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        UserFactory::assert()->empty();
    }

    public function testItFailedExecuteCommandWithExistingEmail(): void
    {
        UserFactory::createOne(['email' => 'user@example.com']);

        self::ensureKernelShutdown();

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:user:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'email' => 'user@example.com',
            'password' => 'Lbn967La@KS=Xt2',
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }
}
