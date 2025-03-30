<?php

namespace App\Controller\Auth;

use App\DTO\User\UserDataDTO;
use App\Entity\Auth\UserToken;
use App\Entity\User\User;
use App\Form\Auth\RegistrationForm;
use App\Repository\User\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/register', name: 'app_register')]
class RegistrationController extends AbstractController
{

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepo,
        Security $security,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {

        // Public only route: redirect to the home page if the user is already authenticated
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegistrationForm::class, new UserDataDTO());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User(
                $form->get('firstName')->getData(),
                $form->get('lastName')->getData(),
                $form->get('email')->getData()
            );

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setUserToken(new UserToken($user, uniqid()));
            $userRepo->save($user, true);

            // Send the verification email
            $email = (new TemplatedEmail())
                ->from($_ENV['MAILER_FROM_ADDRESS'])
                ->to($user->getEmail())
                ->subject($translator->trans('Verify your email'))
                ->htmlTemplate('emails/verify_email.html.twig')
                ->context(
                    [
                        'token' => $user->getUserToken()->getToken(),
                    ]
                );

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->logger->error('Mailer error: ' . $e->getMessage());
                throw new \Exception("Error Processing Request", 1, $e);
            }

            // Log the user in
            return $security->login($user, 'form_login', 'main');
        }

        return $this->render(
            'auth/register.html.twig', [
                'form' => $form,
            ]
        );
    }
}
