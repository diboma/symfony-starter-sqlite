<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ResetPasswordRequestFormType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email', EmailType::class, [
                'attr' => ['autocomplete' => 'email', 'class' => 'form-control'],
                'label' => ucfirst($this->translator->trans('email')),
                'label_attr' => [
                'class' => 'form-label'
                ],
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
                // new Regex([
                //   'pattern' => '/^[a-zA-Z0-9._%+-]+@sumocoders\.be$/',
                //   'message' => $this->translator->trans('email_domain_error', ['{{ domain }}' => 'sumocoders.be']),
                // ]),
                ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
