<?php

namespace App\Controller\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{

  #[Route('/profile', name: 'app_profile')]
  public function index(TranslatorInterface $translator): Response
  {
    // Get the authenticated user
    $user = $this->getUser();
    if (null === $user) {
      throw $this->createAccessDeniedException($translator->trans('You must be logged in to access this page.'));
    }

    // Render the template
    return $this->render('profile/index.html.twig', [
      'controller_name' => 'ProfileController',
    ]);
  }
}
