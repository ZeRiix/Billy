<?php

namespace App\Services\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class GeneratePdfService 
{
	public const IMAGEPATH = '/var/www/public/storage/images/organizations/';
	public function generatePdf(string $html)
	{
		$options = new Options();
		$options->set('chroot', self::IMAGEPATH);
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->render();
		return $dompdf;
	}
}