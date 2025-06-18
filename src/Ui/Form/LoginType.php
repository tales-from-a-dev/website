<?php

declare(strict_types=1);

namespace App\Ui\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<mixed>
 */
final class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_email', EmailType::class, [
                'label' => 'label.email',
                'attr' => [
                    'autocomplete' => 'email',
                ],
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'label.password',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'form',
                'csrf_token_id' => 'authenticate',
                'csrf_field_name' => '_csrf_token',
            ]);
    }
}
