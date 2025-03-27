<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use Dompdf\Dompdf;
use Dompdf\Options;
use Dreadnip\ChromePdfBundle\Service\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/products/{id}/download', name: 'app_products_download')]
class DownloadController extends AbstractController
{
    public function __invoke(Product $product, PdfGenerator $pdfGenerator): Response
    {
        // Render the template
        $html = $this->renderView(
            'product/download.html.twig', [
            'product' => $product,
            ]
        );
        $bootstrapPath = $this->getParameter('kernel.project_dir') . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css';
        $html = str_replace(
            '</head>',
            '<link rel="stylesheet" href="file://' . $bootstrapPath . '"></head>',
            $html
        );

        $useChromePdfBundle = true;

        if ($useChromePdfBundle) {
            // ChromePdfBundle
            $tempFilePath = $this->getParameter('kernel.cache_dir') . '/../tmp/order-' . uniqid() . '.pdf';
            $pdfGenerator->generate($html, $tempFilePath);

            $response = new BinaryFileResponse($tempFilePath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $product->getName() . '.pdf'
            );
            $response->deleteFileAfterSend(true);

            return $response;
        } else {
            // Dompdf
            $options = new Options();
            $options->set('defaultFont', 'Helvetica');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            // Create the PDF
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $pdfContent = $dompdf->output();

            return new Response(
                $pdfContent,
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
}
