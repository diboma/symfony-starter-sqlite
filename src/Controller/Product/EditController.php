<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use App\Form\ProductFormType;
use App\Repository\Product\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/products/{id}/edit', name: 'app_products_edit')]
class EditController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo,
    ) {
    }

    public function __invoke(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepo->save($product, true);
            return $this->redirectToRoute('app_products_index');
        }

        return $this->render(
            'product/edit.html.twig', [
            'form' => $form,
            ]
        );
    }
}
