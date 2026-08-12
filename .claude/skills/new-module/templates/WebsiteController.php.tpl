<?php

declare(strict_types=1);

namespace App\{{Module}}\Ui\Controller\Website;

use App\{{Module}}\Infrastructure\Repository\{{Module}}Repository;
use App\Shared\Ui\Controller\AbstractController;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'index',
    options: [
        'sitemap' => [
            'priority' => 0.8,
            'changefreq' => UrlConcrete::CHANGEFREQ_MONTHLY,
        ],
    ],
    methods: [
        Request::METHOD_GET,
    ]
)]
final class IndexController extends AbstractController
{
    public function __construct(
        private readonly {{Module}}Repository ${{module}}Repository,
    ) {
    }

    public function __invoke(): Response
    {
        return $this->render('app/website/{{module}}/index.html.twig', [
            '{{module}}s' => $this->{{module}}Repository->findAll(),
        ]);
    }
}
