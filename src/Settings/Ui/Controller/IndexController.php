<?php

declare(strict_types=1);

namespace App\Settings\Ui\Controller;

use App\Settings\Domain\Entity\Settings;
use App\Settings\Domain\Enum\SettingsRouteNameEnum;
use App\Settings\Infrastructure\State\Processor\UpdateSettingsProcessor;
use App\Settings\Ui\Form\Data\SettingsDto;
use App\Settings\Ui\Form\Type\SettingsType;
use App\Shared\Domain\Enum\AlertStatusEnum;
use App\Shared\Ui\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'index',
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST,
    ]
)]
final class IndexController extends AbstractController
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

            return $this->redirectToRoute(SettingsRouteNameEnum::WebsiteSettings->value);
        }

        return $this->render('app/website/settings.html.twig', [
            'form' => $form,
        ]);
    }
}
