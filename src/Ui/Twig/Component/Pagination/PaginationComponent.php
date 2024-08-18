<?php

declare(strict_types=1);

namespace App\Ui\Twig\Component\Pagination;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'pagination:pagination')]
final class PaginationComponent
{
    /**
     * @var SlidingPagination<int, mixed>
     */
    public SlidingPagination $pagination;
}
