<?php

declare(strict_types=1);

namespace App\Tests\Integration\Shared\Ui\Command\Database;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class SeedCommandTest extends KernelTestCase
{
    public function testItSuccessfullyExecuteCommand(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:database:seed');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Seed table "settings"', $output);
        $this->assertStringContainsString('Seed table "experience"', $output);
        $this->assertStringContainsString('Database seeded successfully!', $output);
    }
}
