<?php

namespace App\Controller\Product;

use App\Repository\Product\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/products/autocomplete', name: 'app_product_autocomplete')]
class AutocompleteController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $search = $request->query->get('query', '');
        $products = $this->productRepo->findByNameLike($search);

        $suggestions = [];
        foreach ($products as $product) {
            $suggestions[] = ['value' => $product->getId(), 'label' => $product->getName()];
        }

        return $this->json($suggestions);
    }
}
