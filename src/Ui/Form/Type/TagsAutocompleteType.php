<?php

declare(strict_types=1);

namespace App\Ui\Form\Type;

use App\Core\Enum\Role;
use App\Domain\Blog\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
final class TagsAutocompleteType extends AbstractType
{
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Tag::class,
            'autocomplete' => true,
            'searchable_fields' => ['name'],
            'security' => Role::User->value,
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
