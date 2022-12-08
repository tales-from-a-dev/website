<?php

declare(strict_types=1);

namespace App\Http\Controller\Website\Contact;

use App\Core\Enum\AlertType;
use App\Domain\Contact\ContactManager;
use App\Http\Controller\AbstractController;
use App\Http\Form\ContactType;
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

                $this->addAlert(AlertType::Success, 'contact.send.success');
            } catch (\Exception) {
                $this->addAlert(AlertType::Error, 'contact.send.error');
            }

            return $this->redirectToRoute('app_website_contact_index');
        }

        return $this->render('website/contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
