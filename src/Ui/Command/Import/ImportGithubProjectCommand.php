<?php

declare(strict_types=1);

namespace App\Ui\Command\Import;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType;
use App\Domain\Project\Model\GitHubProject;
use App\Domain\Project\ProjectManager;
use App\Infrastructure\GitHub\GitHubService;
use App\Ui\Command\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import:github-project',
    description: 'Import pinned repositories from GitHub.',
)]
final class ImportGithubProjectCommand extends AbstractCommand
{
    public function __construct(
        private readonly GitHubService $gitHubService,
        private readonly ProjectManager $projectManager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->info('Start GitHub project import');

        $totalImport = 0;
        foreach ($this->gitHubService->getPinnedRepositories() as $pinnedRepository) {
            if (null !== $project = $this->projectManager->getRepository()->findOneByGithubId($pinnedRepository['id'])) {
                $this->io->writeln([
                    '',
                    \sprintf('Project "%s" already exists. Skipped', $project->getTitle()),
                    '',
                ]);

                continue;
            }

            $project = (new Project())
                ->setType(ProjectType::GitHub)
                ->setTitle($pinnedRepository['name'])
                ->setDescription($pinnedRepository['description'])
                ->setUrl($pinnedRepository['url'])
                ->setMetadata(new GitHubProject(
                    id: $pinnedRepository['id'],
                    forkCount: $pinnedRepository['forkCount'],
                    stargazerCount: $pinnedRepository['stargazerCount'],
                    languages: [$pinnedRepository['languages']['nodes'][0]['name']]
                ))
            ;

            $this->projectManager->create($project);
            $this->io->writeln([
                '',
                \sprintf('Project "%s" imported.', $project->getTitle()),
                '',
            ]);

            ++$totalImport;
        }

        $this->io->success('Import finished');
        $this->io->writeln([
            '',
            \sprintf('Total import: %d', $totalImport),
            '',
        ]);

        return Command::SUCCESS;
    }
}
