<?php

namespace App\Service;

use App\Entity\Event;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class PdfHelper
{
    private $twig;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(Environment $twig, KernelInterface $kernel)
    {
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    public function createPdfFromTemplate(string $template, array $context, string $filename, string $directory): string
    {
        $pdfOptions = new Options();

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->twig->render($template, $context);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4');

        $dompdf->render();

        $output = $dompdf->output();

        $pdfFilepath = $directory.'/'.$filename.'.pdf';

        file_put_contents($pdfFilepath, $output);

        return $pdfFilepath;
    }

    public function createPdfFromEvent(Event $event): string
    {
        $storeDirectory = $this->kernel->getProjectDir().'/store';

        return $this->createPdfFromTemplate(
            'app/event/pdf.html.twig',
            [
                'event' => $event,
            ],
            $event->getTitle(),
            $storeDirectory
        );
    }
}
