<?php

declare(strict_types=1);

namespace App\Ui\Form;

use App\Domain\Dto\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<SettingsDto>
 */
final class SettingsType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('available', CheckboxType::class, [
                'label' => 'label.available',
                'required' => false,
            ])
            ->add('availableAt', DateType::class, [
                'label' => 'label.available_at',
                'format' => 'yyyy-MM-dd',
                'input' => 'datetime_immutable',
                'required' => false,
            ])
            ->add('averageDailyRate', NumberType::class, [
                'label' => 'label.average_daily_rate',
                'html5' => true,
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SettingsDto::class,
            'empty_data' => new SettingsDto(),
            'translation_domain' => 'form',
            'attr' => [
                'class' => 'max-w-xl',
            ],
        ]);
    }
}
