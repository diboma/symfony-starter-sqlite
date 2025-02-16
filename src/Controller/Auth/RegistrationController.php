<?php

namespace App\Controller\Auth;

use App\Entity\User\User;
use App\Entity\Auth\UserToken;
use Psr\Log\LoggerInterface;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
  private $logger;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  #[Route('/register', name: 'app_register')]
  public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    Security $security,
    EntityManagerInterface $entityManager,
    MailerInterface $mailer,
    TranslatorInterface $translator
  ): Response {

    // Public only route: redirect to the home page if the user is already authenticated
    if ($this->getUser()) {
      return $this->redirectToRoute('app_home');
    }

    // Create the form and handle form submission
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    // Create user if the form was submitted and was valid
    if ($form->isSubmitted() && $form->isValid()) {
      // Encode the plain password
      $user->setPassword(
        $userPasswordHasher->hashPassword(
          $user,
          $form->get('password')->getData()
        )
      );

      // Set the roles
      $user->setRoles(['ROLE_USER']);
      $entityManager->persist($user);

      // Create a verification token
      $userToken = new UserToken();
      $userToken->setEmail($user->getEmail());
      $userToken->setToken(uniqid());
      $userToken->setCreatedAt(new \DateTime());
      $entityManager->persist($userToken);

      // Save all changes to the database (flush)
      $entityManager->flush();

      // Send the verification email
      $email = (new TemplatedEmail())
        ->from($_ENV['MAILER_FROM_ADDRESS'])
        ->to($user->getEmail())
        ->subject($translator->trans('Verify your email'))
        ->htmlTemplate('emails/verify_email.html.twig')
        ->context([
          'token' => $userToken->getToken()
        ]);

      try {
        $mailer->send($email);
      } catch (TransportExceptionInterface $e) {
        $this->logger->error('Mailer error: ' . $e->getMessage());
        throw new \Exception("Error Processing Request", 1, $e);
      }

      // Log the user in
      return $security->login($user, 'form_login', 'main');
    }

    // Render the registration form
    return $this->render('auth/register.html.twig', [
      'registrationForm' => $form,
    ]);
  }
}
