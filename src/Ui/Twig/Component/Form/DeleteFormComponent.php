<?php

declare(strict_types=1);

namespace App\Ui\Twig\Component\Form;

use App\Core\Entity\Behavior\IdentifiableInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent(name: 'form:delete_form')]
final class DeleteFormComponent
{
    public IdentifiableInterface $entity;
    #[ExposeInTemplate(name: 'confirm_text')]
    public ?string $confirmText = null;
    #[ExposeInTemplate(name: 'btn_modal_label')]
    public ?string $buttonModalLabel = null;
    #[ExposeInTemplate(name: 'btn_modal_class')]
    public ?string $buttonModalClass = null;
    #[ExposeInTemplate(name: 'btn_confirm_label')]
    public ?string $buttonConfirmLabel = null;
    #[ExposeInTemplate(name: 'btn_cancel_label')]
    public ?string $buttonCancelLabel = null;

    public function __construct(
        private readonly RequestStack $request
    ) {
    }

    #[ExposeInTemplate(name: 'modal_identifier')]
    public function getModalIdentifier(): string
    {
        return \sprintf('delete-modal-%d', $this->entity->getId());
    }

    #[ExposeInTemplate(name: 'delete_route')]
    public function getDeleteRoute(): string
    {
        $route = str_starts_with((string) $this->request->getCurrentRequest()?->attributes->get('_route'), 'app_admin_')
            ? 'app_admin_%s_delete'
            : 'app_website_%s_delete';

        return \sprintf($route, $this->entity->getEntityName());
    }
}
