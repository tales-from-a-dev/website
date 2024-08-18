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
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/project/new',
    name: 'project_new',
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class NewController extends AbstractController
{
    public function __invoke(Request $request, ProjectRepository $repository): Response
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($project, true);

            $this->addToast(AlertStatus::Success, 'new.success', [
                'selector' => $project->getEntityName(),
                'name' => $project,
            ]);

            return $this->redirectToRoute('app_admin_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }
}
