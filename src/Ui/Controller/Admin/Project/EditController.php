<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Project;

use App\Core\Enum\AlertStatus;
use App\Domain\Project\Entity\Project;
use App\Domain\Project\Repository\ProjectRepository;
use App\Ui\Controller\AbstractController;
use App\Ui\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/project/{id}/edit',
    name: 'project_edit',
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class EditController extends AbstractController
{
    public function __invoke(Request $request, Project $project, ProjectRepository $repository): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($project, true);

            $this->addToast(AlertStatus::Success, 'edit.success', [
                'selector' => $project->getEntityName(),
                'name' => $project,
            ]);
        }

        return $this->render('admin/project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }
}
