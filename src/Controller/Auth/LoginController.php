<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/login', name: 'app_login')]
class LoginController extends AbstractController
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
    ) {
    }

    public function __invoke(): Response
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
}
