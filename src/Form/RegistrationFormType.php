<?php

namespace App\Form;

use App\DTO\User\UserDataDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
  public function __construct(private TranslatorInterface $translator) {}

  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('firstName', TextType::class, [
        'label' => ucfirst($this->translator->trans('firstName')),
        // 'attr' => ['class' => 'form-control'],
        'constraints' => [
          new NotBlank([
            'message' => $this->translator->trans('Please enter your first name')
          ]),
          new Length([
            'max' => 255,
            'maxMessage' => $this->translator->trans('Your firstname should have {{ limit }} characters or less'),
          ]),
        ],
      ])
      ->add('lastName', TextType::class, [
        'label' => ucfirst($this->translator->trans('lastName')),
        // 'attr' => ['class' => 'form-control'],
        'constraints' => [
          new NotBlank([
            'message' => $this->translator->trans('Please enter your last name')
          ]),
          new Length([
            'max' => 255,
            'maxMessage' => $this->translator->trans('Your firstname should have {{ limit }} characters or less'),
          ]),
        ],
      ])
      ->add('email', EmailType::class, [
        'label' => ucfirst($this->translator->trans('email')),
        // 'attr' => ['class' => 'form-control'],
        'constraints' => [
          new NotBlank([
            'message' => $this->translator->trans('Please enter an email address')
          ]),
          new Length([
            'max' => 255,
            'maxMessage' => $this->translator->trans('Your email should have {{ limit }} characters or less'),
          ]),
          // new Regex([
          //   'pattern' => '/^[a-zA-Z0-9._%+-]+@sumocoders\.be$/',
          //   'message' => $this->translator->trans('email_domain_error', ['{{ domain }}' => 'sumocoders.be']),
          // ]),
        ],
      ])
      ->add('password', RepeatedType::class, [
        'type' => PasswordType::class,
        'invalid_message' => $this->translator->trans('The password fields must match'),
        // 'options' => ['attr' => ['class' => 'form-control']],
        'required' => true,
        'first_options'  => [
          'label' => ucfirst($this->translator->trans('password')),
          'toggle' => true,
          'visible_label' => null,
          'hidden_label' => null,
        ],
        'second_options' => [
          'label' => ucfirst($this->translator->trans('repeat_password')),
          'toggle' => true,
          'visible_label' => null,
          'hidden_label' => null,
        ],
        'constraints' => [
          new NotBlank([
            'message' => $this->translator->trans('Please enter a password')
          ]),
          new Length([
            'min' => 8,
            'minMessage' => $this->translator->trans('Your password should be at least {{ limit }} characters'),
            'max' => 255,
          ]),
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => UserDataDTO::class,
    ]);
  }
}
