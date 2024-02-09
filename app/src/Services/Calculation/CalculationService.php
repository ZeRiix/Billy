<?php

namespace App\Services\Calculation;

use Doctrine\Common\Collections\Collection;

class CalculationService
{
	public function doCalculationForTotalHT(Collection $commandes, int $discount)
	{
		$totalHt = 0;
		foreach ($commandes as $commande) {
			$totalHt += $commande->getQuantity() * $commande->getUnitPrice();
		}

		if ($discount > 0) {
			$totalHt = $totalHt * (1 - $discount / 100);
		}

		return $totalHt;
	}
}
