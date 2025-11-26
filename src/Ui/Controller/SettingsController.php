<?php

declare(strict_types=1);

namespace App\Ui\Controller;

use App\Domain\Entity\Settings;
use App\Domain\Enum\AlertStatusEnum;
use App\Domain\Enum\RouteNameEnum;
use App\Infrastructure\State\Processor\UpdateSettingsProcessor;
use App\Ui\Form\Data\SettingsDto;
use App\Ui\Form\Type\SettingsType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/settings',
    name: RouteNameEnum::WebsiteSettings->value,
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST,
    ]
)]
final class SettingsController extends AbstractController
{
    public function __construct(
        private readonly ObjectMapperInterface $objectMapper,
        private readonly UpdateSettingsProcessor $processor,
    ) {
    }

    public function __invoke(
        Request $request,
        #[MapEntity(expr: 'repository.findFirst()')]
        Settings $settings,
    ): Response {
        $dto = $this->objectMapper->map($settings, SettingsDto::class);

        $form = $this->createForm(SettingsType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processor->process($form->getData(), ['previous_data' => $settings]);

            $this->addAlert(AlertStatusEnum::Success, 'settings.update.success');

            return $this->redirectToRoute(RouteNameEnum::WebsiteSettings->value);
        }

        return $this->render('app/website/settings.html.twig', [
            'form' => $form,
        ]);
    }
}
