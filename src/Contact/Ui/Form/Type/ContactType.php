<?php

declare(strict_types=1);

namespace App\Contact\Ui\Form\Type;

use App\Contact\Ui\Form\Data\ContactDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ContactDto>
 */
final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'label.full_name',
                'label_attr' => [
                    'class' => "after:content-['*'] after:ml-0.5 after:text-red-500",
                ],
            ])
            ->add('company', TextType::class, [
                'label' => 'label.company',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
                'label_attr' => [
                    'class' => "after:content-['*'] after:ml-0.5 after:text-red-500",
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'label.content',
                'label_attr' => [
                    'class' => "after:content-['*'] after:ml-0.5 after:text-red-500",
                ],
                'attr' => [
                    'placeholder' => 'placeholder.contact.content',
                    'rows' => 8,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDto::class,
            'empty_data' => new ContactDto(),
            'translation_domain' => 'form',
            'antispam_profile' => 'default',
            'attr' => [
                'id' => 'contact-form',
            ],
        ]);
    }
}
