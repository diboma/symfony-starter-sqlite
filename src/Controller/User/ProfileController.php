<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
#[Route('/profile', name: 'app_profile')]
class ProfileController extends AbstractController
{

    public function __invoke(TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        if (null === $user) {
            throw $this->createAccessDeniedException($translator->trans('You must be logged in to access this page.'));
        }

        return $this->render('profile/index.html.twig');
    }
}
