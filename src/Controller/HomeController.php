<?php

namespace App\Controller;

use App\Form\Product\AutocompleteForm;
use App\Repository\Product\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/', name: 'app_home')]
class HomeController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo,
    ) {
    }

    public function __invoke(): Response
    {
        $formAutocomplete = $this->createForm(AutocompleteForm::class);
        $allProducts = $this->productRepo->findAll();

        return $this->render(
            'home/index.html.twig', [
                'controller_name' => 'HomeController',
                'form_autocomplete' => $formAutocomplete,
                'allProducts' => $allProducts,
            ]
        );
    }
}
