<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/products/{id}', name: 'app_products_show')]
class ShowController extends AbstractController
{
    public function __invoke(Product $product): Response
    {
        return $this->render(
            'product/show.html.twig', [
            'product' => $product,
            ]
        );
    }
}
