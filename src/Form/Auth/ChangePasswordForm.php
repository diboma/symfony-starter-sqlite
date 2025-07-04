<?php

namespace App\Form\Auth;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordForm extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'options' => [
                        'attr' => [
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'first_options' => [
                        'constraints' => [
                            new NotBlank(
                                [
                                    'message' => $this->translator->trans('Please enter a password'),
                                ]
                            ),
                            new Length(
                                [
                                    'min' => 8,
                                    'minMessage' => $this->translator->trans('Your password should be at least {{ limit }} characters'),
                                    // max length allowed by Symfony for security reasons
                                    'max' => 4096,
                                ]
                            ),
                            // new PasswordStrength(),
                            // new NotCompromisedPassword(),
                        ],
                        'label' => ucfirst($this->translator->trans('new_password')),
                    ],
                    'second_options' => [
                        'label' => ucfirst($this->translator->trans('repeat_password')),
                    ],
                    'invalid_message' => $this->translator->trans('The password fields must match'),
                    // Instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
