<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use App\Form\Product\Form;
use App\Repository\Product\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/products/new', name: 'app_products_new')]
class CreateController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepo
    ) {
    }

    public function __invoke(Request $request): Response
    {
        // Create the form and handle form submission
        $product = new Product();
        $form = $this->createForm(Form::class, $product);
        $form->handleRequest($request);

        // Create product if the form was submitted and was valid
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepo->save($product, true);
            return $this->redirectToRoute('app_products_index');
        }

        // Render the template to create a new product
        return $this->render(
            'product/new.html.twig', [
                'form' => $form,
            ]
        );
    }
}
