<?php

namespace App\Controller\Product;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Form\ProductFormType;
use App\Entity\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

  /**
   * Download PDF of a product
   */
  #[Route('/products/{id}/download', name: 'app_products_download')]
  public function download(Product $product): Response
  {
    // Render the template
    $html = $this->renderView('product/download.html.twig', [
      'product' => $product
    ]);
    $bootstrapPath = $this->getParameter('kernel.project_dir') . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css';
    $html = str_replace(
      '</head>',
      '<link rel="stylesheet" href="file://' . $bootstrapPath . '"></head>',
      $html
    );

    // Set options
    $options = new Options();
    $options->set('defaultFont', 'Helvetica');
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);

    // Create the PDF
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // return new Response(
    //   $dompdf->stream('resume', ["Attachment" => false]),
    //   Response::HTTP_OK,
    //   ['Content-Type' => 'application/pdf']
    // );

    // $dompdf->stream("testpdf.pdf", [
    //   "Attachment" => true
    // ]);

    return new Response(
      $dompdf->output(),
      Response::HTTP_OK,
      [
        'Content-Type' => 'application/pdf',
        // toon de pdf inline, in de browser
        // 'Content-Disposition' => 'inline; filename="' . $product->getName() . '.pdf"',
        // download de pdf
        'Content-Disposition' => 'attachment; filename="' . $product->getName() . '.pdf"',
      ]
    );
  }
}
