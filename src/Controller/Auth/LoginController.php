<?php

namespace App\Controller\Auth;

use App\DTO\Auth\VerifyEmailDTO;
use App\Entity\User\User;
use App\Form\VerifyEmailFormType;
use App\Repository\Auth\UserTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
        private TranslatorInterface $translator,
        private UserTokenRepository $userTokenRepo,
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(): Response
    {
        // Public only route: redirect to the home page if the user is already authenticated
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // Get the last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        // Render the login page
        return $this->render(
            'auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            ]
        );
    }

    #[Route(path: '/verify-email', name: 'app_verify_email')]
    public function verifyEmail(Request $request): Response
    {
        /**
   * @var User $user 
*/
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Handle the form
        $form = $this->createForm(VerifyEmailFormType::class, new VerifyEmailDTO());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $form->get('token')->getData();

            if ($this->userTokenRepo->verifyToken($user, $token)) {
                return $this->redirectToRoute('app_home');
            }

            $this->addFlash(
                'error',
                $this->translator->trans('Something went wrong verifying your email')
            );

            return $this->redirectToRoute(
                'app_verify_email', [
                'email' => $user->getEmail(),
                'form' => $form,
                ]
            );
        }

        if (!$user->isEmailVerified()) {
            return $this->render(
                'auth/verify_email.html.twig', [
                'email' => $user->getEmail(),
                'form' => $form,
                ]
            );
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
