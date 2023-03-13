<?php

declare(strict_types=1);

namespace App\Ui\Twig\Component;

use App\Core\Entity\Behavior\IdentifiableInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(name: 'action_list')]
final class ActionListComponent
{
    public IdentifiableInterface $entity;

    public function __construct(
        private readonly RequestStack $request
    ) {
    }

    #[ExposeInTemplate(name: 'edit_route')]
    public function getDeleteRoute(): string
    {
        $route = str_starts_with($this->request->getCurrentRequest()?->attributes->get('_route'), 'app_admin_')
            ? 'app_admin_%s_edit'
            : 'app_website_%s_edit';

        return sprintf($route, $this->entity->getEntityName());
    }
}
