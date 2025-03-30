<?php

namespace App\Form\Product;

use App\Entity\Product\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteProduct extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'product',
                EntityType::class,
                [
                    'class' => Product::class,
                    'choice_label' => 'name',
                    'autocomplete' => true,
                    'multiple' => true,
                ]
            )
            // ->add(
            //     'product_input',
            //     TextType::class,
            //     [
            //         // 'label' => 'Product',
            //         'autocomplete' => true,
            //         'autocomplete_url' => '/autocomplete/products',
            //         // 'attr' => [
            //         //     'data-controller' => 'autocomplete',
            //         //     'data-autocomplete-url-value' => '/products/autocomplete',
            //         // ],
            //     ]
            // )
            ->add('submit', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Product::class,
            ]
        );
    }
}
