<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use App\Form\ProductFormType;
use App\Repository\Product\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProductController extends AbstractController
{

  /**
   * Show all products
   */
  #[Route('/products', name: 'app_products_index')]
  public function index(ProductRepository $productRepository, Request $request): Response
  {
    // Get all products
    // $products = $productRepository->findAll();
    // $products = $productRepository->findBy([], ['id' => 'DESC']);

    // Get paginated products
    $currentPage = $request->query->getInt('page', 1);
    $limit = 4; // Number of items per page
    $products = $productRepository->paginate($currentPage, $limit);

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
    return $this->render('product/index.html.twig', [
      'products' => $products,
      'paginator' => $paginator,
    ]);
  }

  /**
   * Create a new product
   */
  #[Route('/products/new', name: 'app_products_new')]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    // Create the form and handle form submission
    $product = new Product();
    $form = $this->createForm(ProductFormType::class, $product);
    $form->handleRequest($request);

    // Create product if the form was submitted and was valid
    if ($form->isSubmitted() && $form->isValid()) {
      // Save the product
      $entityManager->persist($product);
      $entityManager->flush();
      // Redirect to the product list
      return $this->redirectToRoute('app_products_index');
    }

    // Render the template to create a new product
    return $this->render('product/new.html.twig', [
      'form' => $form
    ]);
  }

  /**
   * Show a single product
   */
  #[Route('/products/{id}', name: 'app_products_show')]
  public function show(Product $product): Response
  {
    // Render the template
    return $this->render('product/show.html.twig', [
      'product' => $product
    ]);
  }

  /**
   * Edit an existing product
   */
  #[Route('/products/{id}/edit', name: 'app_products_edit')]
  public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
  {
    // Create the form and handle form submission
    $form = $this->createForm(ProductFormType::class, $product);
    $form->handleRequest($request);

    // Update product if the form was submitted and was valid
    if ($form->isSubmitted() && $form->isValid()) {
      // Save the product
      $entityManager->persist($product);
      $entityManager->flush();
      // Redirect to the product list
      return $this->redirectToRoute('app_products_index');
    }


    // Render the template
    return $this->render('product/edit.html.twig', [
      'form' => $form
    ]);
  }

  /**
   * Delete an existing product
   */
  #[Route('/products/{id}/delete', name: 'app_products_delete')]
  public function delete(Product $product, EntityManagerInterface $entityManager): Response
  {
    // Delete the product
    $entityManager->remove($product);
    $entityManager->flush();

    // Redirect to the product list
    return $this->redirectToRoute('app_products_index');
  }
}
