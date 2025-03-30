<?php

namespace App\Form\Product;

use App\Entity\Product\Category;
use App\Entity\Product\Product;
use App\Entity\User\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Contracts\Translation\TranslatorInterface;

class Form extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name', TextType::class, [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => ucfirst($this->translator->trans('name')),
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $this->translator->trans('Please enter a name'),
                            ]
                        ),
                        new Length(
                            [
                                'max' => 255,
                                'maxMessage' => $this->translator->trans('Your name should have {{ limit }} characters or less'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'description', TextType::class, [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => ucfirst($this->translator->trans('description')),
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $this->translator->trans('Please enter a description'),
                            ]
                        ),
                        new Length(
                            [
                                'max' => 4096,
                                'maxMessage' => $this->translator->trans('Your description should have {{ limit }} characters or less'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'price', NumberType::class, [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => ucfirst($this->translator->trans('price')),
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $this->translator->trans('Please enter a price'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'image', UrlType::class, [
                    'default_protocol' => 'https',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => $this->translator->trans('Image URL'),
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'required' => false,
                    'constraints' => [
                        new Length(
                            [
                                'max' => 255,
                                'maxMessage' => $this->translator->trans('Your image should have {{ limit }} characters or less'),
                            ]
                        ),
                        new Url(
                            [
                                'message' => $this->translator->trans('Please enter a valid URL'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'owner', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => function (User $user) {
                        return $user->getFullName();
                    },
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('owner')
                            ->orderBy('owner.firstName', 'ASC');
                    },
                    'choice_value' => function (?User $owner) {
                        return $owner ? $owner->getId() : '';
                    },
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => ucfirst($this->translator->trans('owner')),
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $this->translator->trans('Please enter an owner'),
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'category', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('query')
                            ->orderBy('query.name', 'ASC');
                    },
                    'choice_value' => function (?Category $category) {
                        return $category !== null ? $category->getId() : '';
                    },
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => ucfirst($this->translator->trans('category')),
                    'label_attr' => [
                        'class' => 'form-label',
                    ],
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => $this->translator->trans('Please enter a category'),
                            ]
                        ),
                    ],
                ]
            );
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
