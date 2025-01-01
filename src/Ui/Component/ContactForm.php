<?php

declare(strict_types=1);

namespace App\Ui\Component;

use App\Domain\Dto\ContactDto;
use App\Domain\Enum\AlertStatusEnum;
use App\Domain\Service\ContactServiceInterface;
use App\Ui\Controller\AbstractController;
use App\Ui\Form\ContactType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ContactForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?ContactDto $initialFormData = null;

    #[LiveAction]
    public function send(ContactServiceInterface $contactService): void
    {
        $this->submitForm();

        try {
            $contactService->notify($this->getForm()->getData());

            $this->addAlert(AlertStatusEnum::Success, 'contact.send.success');
        } catch (\Exception) {
            $this->addAlert(AlertStatusEnum::Danger, 'contact.send.error');
        }

        $this->resetForm();
    }

    protected function instantiateForm(): FormInterface
    {
        return $this
            ->createForm(ContactType::class, $this->initialFormData)
            ->add('send', SubmitType::class, [
                'label' => 'button.send',
            ]);
    }
}
