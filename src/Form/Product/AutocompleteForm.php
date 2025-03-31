<?php

namespace App\Form\Product;

use App\Entity\Product\Product;
use App\Repository\Product\ProductRepository;
// use App\Form\Product\AutocompleteField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutocompleteForm extends AbstractType
{
    public function __construct(
        private ProductRepository $productRepo
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // ->add('product', AutocompleteField::class)
            ->add(
                'product',
                EntityType::class,
                [
                    // 'label' => 'EntityType',
                    'class' => Product::class,
                    'attr' => [
                        'data-role' => 'autocomplete-product',
                    ],
                    'autocomplete' => true,
                    'multiple' => true,
                    'choice_label' => 'name',
                    // 'choice_value' => function (Product $product) {
                    //     // return $product->getName();
                    //     // return $product->getCategory()->getName();
                    //     return $product->getId() . '|' . $product->getCategory()->getId();
                    // },
                    'choice_attr' => function ($product) {
                        return [
                            'data-product-id' => $product->getId(),
                            'data-product-name' => $product->getName(),
                            'data-category-id' => $product->getCategory()->getId(),
                        ];
                    },
                ]
            )
            // ->add(
            //     'product_choice',
            //     ChoiceType::class,
            //     [
            //         'label' => 'ChoiceType',
            //         'autocomplete' => true,
            //         'multiple' => true,
            //         'choices' => $this->productRepo->findAll(),
            //         'choice_label' => function (Product $product) {
            //             return $product->getName();
            //         },
            //         // 'choice_value' => function (Product $product) {
            //         //     // return $product->getName();
            //         //     // return $product->getCategory()->getName();
            //         //     return $product->getId() . '|' . $product->getCategory()->getId();
            //         // },
            //         'choice_attr' => function ($product) {
            //             return [
            //                 'data-product-id' => $product->getId(),
            //                 'data-product-name' => $product->getName(),
            //                 'data-category-id' => $product->getCategory()->getId(),
            //             ];
            //         },
            //     ]
            // )
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
