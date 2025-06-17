<?php

declare(strict_types=1);

namespace App\Ui\Controller;

use App\Domain\Enum\RouteNameEnum;
use App\Domain\Service\ContactServiceInterface;
use App\Ui\Form\ContactType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        private readonly ContactServiceInterface $contactService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        $form = $this->createContactForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $success = true;

            $this->contactService->notify($form->getData());
            //            try {
            //            } catch (\Exception $exception) {
            //                $success = false;
            //
            //               $this->logger->critical($exception->getMessage(), $exception->getTrace());
            //            } finally {
            //
            //            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return true === $success
                    ? $this->renderBlock('app/website/contact.html.twig', 'form_success')
                    : $this->renderBlock('app/website/contact.html.twig', 'form_error')
                ;
            }

            return $this->redirectToRoute(route: RouteNameEnum::WebsiteHome->value, status: Response::HTTP_SEE_OTHER);
        }

        return $this->renderBlock('app/website/contact.html.twig', 'form', [
            'form' => $form,
        ]);
    }

    private function createContactForm(): FormInterface
    {
        return $this
            ->createForm(type: ContactType::class, options: [
                'action' => $this->generateUrl(RouteNameEnum::WebsiteContact->value),
                'method' => Request::METHOD_POST,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'button.send',
                'attr' => [
                    'class' => 'w-full md:w-auto',
                ],
                'row_attr' => [
                    'class' => 'flex justify-end',
                ],
            ])
        ;
    }
}
