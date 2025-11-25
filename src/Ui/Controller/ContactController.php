<?php

declare(strict_types=1);

namespace App\Ui\Controller;

use App\Domain\Enum\AlertStatusEnum;
use App\Domain\Enum\RouteNameEnum;
use App\Infrastructure\State\Processor\SendContactProcessor;
use App\Ui\Form\Data\ContactDto;
use App\Ui\Form\Type\ContactType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

#[Route(
    path: '/contact',
    name: RouteNameEnum::WebsiteContact->value,
    methods: [
        Request::METHOD_GET,
        Request::METHOD_POST,
    ]
)]
final class ContactController extends AbstractController
{
    public function __construct(
        private readonly SendContactProcessor $processor,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        $form = $this->createContactForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $success = $this->processor->process($form->getData());

            $this->addAlert(
                status: $success ? AlertStatusEnum::Success : AlertStatusEnum::Error,
                message: $success ? 'contact.send.success' : 'contact.send.error',
            );

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->renderBlock('app/website/contact.html.twig', 'update', [
                    'form' => $this->createContactForm(),
                ]);
            }

            return $this->redirectToRoute(route: RouteNameEnum::WebsiteHome->value, status: Response::HTTP_SEE_OTHER);
        }

        return $this->renderBlock('app/website/contact.html.twig', 'new', [
            'form' => $form,
        ]);
    }

    /**
     * @return FormInterface<ContactDto|null>
     */
    private function createContactForm(): FormInterface
    {
        return $this->createForm(type: ContactType::class, options: [
            'action' => $this->generateUrl(RouteNameEnum::WebsiteContact->value),
            'method' => Request::METHOD_POST,
        ]);
    }
}
