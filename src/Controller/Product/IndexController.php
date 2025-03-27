<?php

namespace App\Controller\Product;

use App\Repository\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/products', name: 'app_products_index')]
class IndexController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo
    ) {
    }

    public function __invoke(Request $request): Response
    {
        // Get all products
        // $products = $this->productRepo->findAll();
        // $products = $this->productRepo->findBy([], ['id' => 'DESC']);

        // Get paginated products
        $currentPage = $request->query->getInt('page', 1);
        $limit = 4; // Number of items per page
        $products = $this->productRepo->paginate($currentPage, $limit);

        // Get pagination data
        $totalProducts = $products->count();
        $totalPages = ceil($totalProducts / $limit);
        $hasPreviousPage = $currentPage > 1;
        $hasNextPage = $currentPage < $totalPages;

        $paginator = (object) [
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'hasPreviousPage' => $hasPreviousPage,
        'hasNextPage' => $hasNextPage,
        ];

        // Render the template
        return $this->render(
            'product/index.html.twig', [
            'products' => $products,
            'paginator' => $paginator,
            ]
        );
    }
}
