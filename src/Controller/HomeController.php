<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/', name: 'app_home')]
class HomeController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render(
            'home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]
        );
    }
}
