<?php

declare(strict_types=1);

namespace App\Ui\Form;

use App\Domain\Project\Entity\Project;
use App\Domain\Project\Enum\ProjectType as ProjectTypeEnum;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'label.title',
            ])
            ->add('subTitle', TextType::class, [
                'label' => 'label.subtitle',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'attr' => [
                    'rows' => 20,
                ],
            ])
            ->add('type', EnumType::class, [
                'label' => 'label.type',
                'class' => ProjectTypeEnum::class,
                'choice_translation_domain' => 'messages',
            ])
            ->add('url', UrlType::class, [
                'label' => 'label.url',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'translation_domain' => 'form',
        ]);
    }
}
