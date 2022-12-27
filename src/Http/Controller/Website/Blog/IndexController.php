<?php

declare(strict_types=1);

namespace App\Http\Controller\Website\Blog;

use App\Domain\Blog\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '/blog',
    name: 'blog_index',
    methods: [Request::METHOD_GET]
)]
final class IndexController extends AbstractController
{
    public function __invoke(Request $request, PostRepository $repository, PaginatorInterface $paginator): Response
    {
        $form = $this->createSearchForm();
        $form->handleRequest($request);

        return $this->render('website/blog/index.html.twig', [
            'form' => $form,
            'posts' => $paginator->paginate(
                $repository->queryAllPublished($request->query->get('search', '')),
                $request->query->getInt('page', 1),
                5
            ),
        ]);
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(SearchType::class, null, [
            'method' => Request::METHOD_GET,
            'label' => false,
            'required' => false,
        ]);
    }
}
