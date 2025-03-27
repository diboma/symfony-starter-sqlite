<?php

namespace App\Form;

use App\DTO\Auth\VerifyEmailDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class VerifyEmailFormType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email', EmailType::class, [
                'label' => ucfirst($this->translator->trans('email')),
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                new NotBlank(
                    [
                    'message' => $this->translator->trans('Please enter an email address')
                    ]
                ),
                new Length(
                    [
                    'max' => 255,
                    'maxMessage' => $this->translator->trans('Your email should have {{ limit }} characters or less'),
                    ]
                ),
                ],
                ]
            )
            ->add(
                'token', TextType::class, [
                'label' => ucfirst($this->translator->trans('token')),
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                new NotBlank(
                    [
                    'message' => $this->translator->trans('Please enter a token')
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
            'data_class' => VerifyEmailDTO::class,
            ]
        );
    }
}
