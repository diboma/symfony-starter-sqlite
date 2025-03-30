<?php

namespace App\Controller;

use App\Form\Product\AutocompleteForm;
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
        $formAutocomplete = $this->createForm(AutocompleteForm::class);

        return $this->render(
            'home/index.html.twig', [
                'controller_name' => 'HomeController',
                'form_autocomplete' => $formAutocomplete,
            ]
        );
    }
}
