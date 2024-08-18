<?php

declare(strict_types=1);

namespace App\Tests\Functional\Ui\Command\Import;

use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportGithubProjectCommandTest extends KernelTestCase
{
    public function testItCanExecuteCommand(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:import:github-project');

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        self::assertStringContainsString('[INFO] Start GitHub project import', $output);
        self::assertStringContainsString('[OK] Import finished', $output);
        self::assertStringContainsString('Total import: 0', $output);

        $projectRepository = static::getContainer()->get(ProjectRepository::class);

        self::assertSame(0, $projectRepository->count(['type' => ProjectType::GitHub]));
    }
}
