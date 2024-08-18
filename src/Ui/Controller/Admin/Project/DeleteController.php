<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Project;

use App\Core\Enum\AlertStatus;
use App\Domain\Project\Entity\Project;
use App\Domain\Project\Repository\ProjectRepository;
use App\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/project/{id}',
    name: 'project_delete',
    methods: [Request::METHOD_DELETE]
)]
final class DeleteController extends AbstractController
{
    public function __invoke(Request $request, Project $project, ProjectRepository $repository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), (string) $request->request->get('_token'))) {
            $repository->remove($project, true);

            $this->addToast(AlertStatus::Success, 'delete.success', [
                'selector' => $project->getEntityName(),
                'name' => $project,
            ]);
        }

        return $this->redirectToRoute('app_admin_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
