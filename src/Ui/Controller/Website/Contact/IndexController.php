<?php

declare(strict_types=1);

namespace App\Ui\Controller\Website\Contact;

use App\Core\Enum\AlertStatus;
use App\Domain\Contact\ContactManager;
use App\Ui\Controller\AbstractController;
use App\Ui\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/contact',
    name: 'contact_index',
    methods: [Request::METHOD_GET, Request::METHOD_POST],
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, ContactManager $contactManager): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $contactManager->notify($form->getData());

                $this->addAlert(AlertStatus::Success, 'contact.send.success');
            } catch (\Exception) {
                $this->addAlert(AlertStatus::Error, 'contact.send.error');
            }

            return $this->redirectToRoute('app_website_contact_index');
        }

        return $this->render('website/contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
