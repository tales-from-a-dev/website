<?php

declare(strict_types=1);

namespace App\Ui\Form;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Enum\PublicationStatus;
use App\Ui\Form\Type\TagsAutocompleteType;
use Elao\Enum\Bridge\Symfony\Form\Type\EnumType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PostType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publicationStatus', EnumType::class, [
                'label' => 'label.publication_status',
                'class' => PublicationStatus::class,
                'choice_translation_domain' => 'messages',
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'label.published_at',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => [
                    'min' => (new \DateTime())->format(\DateTimeInterface::ATOM),
                ],
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label' => 'label.title',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'label.content',
                'attr' => [
                    'rows' => 20,
                ],
            ])
            ->add('tags', TagsAutocompleteType::class, [
                'label' => 'label.tags',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'translation_domain' => 'form',
        ]);
    }
}
