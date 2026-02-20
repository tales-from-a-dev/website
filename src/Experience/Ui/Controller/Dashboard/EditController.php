<?php

declare(strict_types=1);

namespace App\Experience\Ui\Controller\Dashboard;

use App\Experience\Domain\Entity\Experience;
use App\Experience\Domain\Enum\ExperienceRouteNameEnum;
use App\Experience\Infrastructure\State\Processor\UpdateExperienceProcessor;
use App\Experience\Ui\Form\Data\ExperienceDto;
use App\Experience\Ui\Form\Type\ExperienceType;
use App\Shared\Domain\Enum\AlertStatusEnum;
use App\Shared\Ui\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route(
    path: '/edit/{id:experience}',
    name: 'edit',
    requirements: [
        'id' => Requirement::POSITIVE_INT,
    ],
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST,
    ]
)]
class EditController extends AbstractController
{
    public function __construct(
        private readonly ObjectMapperInterface $objectMapper,
        private readonly UpdateExperienceProcessor $processor,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapEntity] Experience $experience,
    ): Response {
        $data = $this->objectMapper->map($experience, ExperienceDto::class);

        $form = $this->createForm(ExperienceType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $experience = $this->processor->process($form->getData(), ['previous_data' => $experience]);

            $this->addAlert(
                status: AlertStatusEnum::Success,
                message: 'experience.update.success',
            );

            return $this->redirectAfterSubmit(ExperienceRouteNameEnum::DashboardEdit, [
                'id' => $experience->id,
            ]);
        }

        return $this->render('app/dashboard/experience/edit.html.twig', [
            'experience' => $experience,
            'form' => $form,
        ]);
    }
}
