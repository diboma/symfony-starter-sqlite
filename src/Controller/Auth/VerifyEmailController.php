<?php

namespace App\Controller\Auth;

use App\DTO\Auth\VerifyEmailDTO;
use App\Entity\User\User;
use App\Form\Auth\VerifyEmailForm;
use App\Repository\Auth\UserTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/verify-email', name: 'app_verify_email')]
class VerifyEmailController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private UserTokenRepository $userTokenRepo,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Handle the form
        $form = $this->createForm(VerifyEmailForm::class, new VerifyEmailDTO());
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
}
