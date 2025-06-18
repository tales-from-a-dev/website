<?php

declare(strict_types=1);

namespace App\Ui\Controller;

use App\Domain\Enum\AlertStatusEnum;
use App\Domain\ValueObject\Alert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Translation\TranslatableMessage;

abstract class AbstractController extends BaseAbstractController
{
    /**
     * @param array<string, mixed> $parameters
     */
    protected function addAlert(AlertStatusEnum $status, string|TranslatableMessage $message, array $parameters = []): void
    {
        if (\is_string($message)) {
            $message = new TranslatableMessage($message, $parameters, 'alert');
        }

        $this->addFlash('alert', new Alert($message, $status));
    }

    /**
     * Creates and returns a named form instance from the type of the form.
     *
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param array<string, mixed>                   $options
     *
     * @return FormInterface<mixed>
     */
    protected function createFormNamed(string $name, string $type = FormType::class, mixed $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->createNamed($name, $type, $data, $options);
    }
}
