<?php

namespace App\Form\Product;

use App\Entity\Product\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class AutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'class' => Product::class,
                'autocomplete' => true,
                'multiple' => true,
                'choice_label' => 'name',
                // 'choice_value' => 'id|' . fn(Product $product) => $product->getCategory()->getId(),
                'choice_attr' => function (Product $product) {
                    return ['data-category-id' => $product->getCategory()->getId()];
                },
            ]
        );
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
