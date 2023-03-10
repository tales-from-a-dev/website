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
    path: '/tag/{id}/edit',
    name: 'tag_edit',
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class EditController extends AbstractController
{
    public function __invoke(Request $request, Tag $tag, TagRepository $repository): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->save($tag, true);

            $this->addToast(AlertStatus::Success, 'edit.success', [
                'selector' => $tag->getEntityName(),
                'name' => $tag,
            ]);
        }

        return $this->render('admin/tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form,
        ]);
    }
}
