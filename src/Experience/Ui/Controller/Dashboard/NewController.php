<?php

declare(strict_types=1);

namespace App\Experience\Ui\Controller\Dashboard;

use App\Experience\Domain\Enum\ExperienceRouteNameEnum;
use App\Experience\Infrastructure\State\Processor\CreateExperienceProcessor;
use App\Experience\Ui\Form\Type\ExperienceType;
use App\Shared\Ui\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/new',
    name: 'new',
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST,
    ]
)]
class NewController extends AbstractController
{
    public function __construct(
        private readonly CreateExperienceProcessor $processor,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        $form = $this->createForm(ExperienceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $experience = $this->processor->process($form->getData());

            return $this->redirectAfterSubmit(ExperienceRouteNameEnum::DashboardEdit, [
                'id' => $experience->id,
            ]);
        }

        return $this->render('app/dashboard/experience/new.html.twig', [
            'form' => $form,
        ]);
    }
}
