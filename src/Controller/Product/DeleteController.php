<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use App\Repository\Product\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/products/{id}/delete', name: 'app_products_delete')]
class DeleteController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo,
    ) {
    }

    public function __invoke(Product $product): Response
    {
        $this->productRepo->delete($product);
        return $this->redirectToRoute('app_products_index');
    }
}
