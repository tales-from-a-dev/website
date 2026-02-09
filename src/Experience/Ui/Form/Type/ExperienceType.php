<?php

declare(strict_types=1);

namespace App\Experience\Ui\Form\Type;

use App\Experience\Domain\Enum\ExperiencePositionEnum;
use App\Experience\Domain\Enum\ExperienceTypeEnum;
use App\Experience\Ui\Form\Data\ExperienceDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\String\s;

/**
 * @extends AbstractType<ExperienceDto>
 */
class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company', TextType::class, [
                'label' => 'label.company',
            ])
            ->add('type', EnumType::class, [
                'class' => ExperienceTypeEnum::class,
                'label' => 'label.experience_type',
            ])
            ->add('position', EnumType::class, [
                'class' => ExperiencePositionEnum::class,
                'label' => 'label.experience_position',
            ])
            ->add('technologies', TextType::class, [
                'label' => 'label.technologies',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description',
                'required' => false,
                'attr' => [
                    'rows' => 8,
                ],
                'empty_data' => '',
            ])
            ->add('startAt', DateType::class, [
                'label' => 'label.start_at',
                'format' => 'yyyy-MM-dd',
                'input' => 'datetime_immutable',
            ])
            ->add('endAt', DateType::class, [
                'label' => 'label.end_at',
                'format' => 'yyyy-MM-dd',
                'input' => 'datetime_immutable',
                'required' => false,
            ])
        ;

        $builder->get('technologies')
            ->addModelTransformer(new CallbackTransformer(
                static function (?array $tagsAsArray): string {
                    // transform the array to a string
                    return s(', ')->join($tagsAsArray ?? [])->trim()->toString();
                },
                static function (string $tagsAsString): array {
                    // transform the string back to an array
                    return s($tagsAsString)->trim()->split(', ');
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExperienceDto::class,
            'empty_data' => new ExperienceDto(),
            'translation_domain' => 'forms',
            'attr' => [
                'data-slot' => 'field-group',
                'class' => 'group/field-group @container/field-group flex w-full flex-col gap-7 data-[slot=checkbox-group]:gap-3 [&>[data-slot=field-group]]:gap-4',
            ],
        ]);
    }
}
