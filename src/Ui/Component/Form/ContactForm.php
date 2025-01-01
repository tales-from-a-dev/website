<?php

declare(strict_types=1);

namespace App\Ui\Component\Form;

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
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?ContactDto $initialFormData = null;

    #[LiveAction]
    public function send(ContactServiceInterface $contactService): void
    {
        $this->submitForm();

        try {
            $contactService->notify($this->getForm()->getData());

            $this->addAlert(AlertStatusEnum::Success, 'contact.send.success');

            $this->resetForm();
        } catch (\Exception) {
            $this->addAlert(AlertStatusEnum::Danger, 'contact.send.error');
        }
    }

    protected function instantiateForm(): FormInterface
    {
        return $this
            ->createForm(ContactType::class, $this->initialFormData)
            ->add('send', SubmitType::class, [
                'label' => 'button.send',
                'attr' => [
                    'class' => 'w-full md:w-auto',
                ],
                'row_attr' => [
                    'class' => 'flex justify-end',
                ],
            ]);
    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
    }
}
