<?php

namespace App\Services\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class GeneratePdfService
{
	public const IMAGE_PATH = "/var/www/public/storage/images/organizations/";
	public const DEFAULT_IMAGE_PATH = "/var/www/public/assets/images/default.jpg";

	public function generatePdf(string $html)
	{
		$options = new Options();
		$options->set("chroot", [self::IMAGE_PATH, self::DEFAULT_IMAGE_PATH]);
		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);
		$dompdf->render();
		return $dompdf;
	}
}
