<?php

declare(strict_types=1);

namespace App\Ui\Controller\Admin\Tag;

use App\Core\Enum\AlertStatus;
use App\Domain\Blog\Entity\Tag;
use App\Domain\Blog\Repository\TagRepository;
use App\Ui\Controller\AbstractController;
use App\Ui\Form\TagType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/tag/new',
    name: 'tag_new',
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class NewController extends AbstractController
{
    public function __invoke(Request $request, TagRepository $repository): Response
    {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($tag, true);

            $this->addToast(AlertStatus::Success, 'new.success', [
                'selector' => $tag->getEntityName(),
                'name' => $tag,
            ]);

            return $this->redirectToRoute('app_admin_tag_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }
}
