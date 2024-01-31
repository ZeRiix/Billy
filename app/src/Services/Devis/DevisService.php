<?php 

namespace App\Services\Devis;

use App\Entity\Devis;
use App\Entity\Organization;
use App\Repository\DevisRepository;

class DevisService {

	private DevisRepository $devisRepository;

	public function __construct(
		DevisRepository $devisRepository
	) {
		$this->devisRepository = $devisRepository;
	}

	public function create(Devis $devis, Organization $organization) : void {
		$devis->setOrganization($organization);
		$this->devisRepository->save($devis);
	}

	public function update(Devis $devis) : void {
		if ($devis->getDiscount() < 0 || $devis->getDiscount() > 100) {
			throw new \Exception("Le taux de remise doit être compris entre 0 et 100.");
		}
		$this->devisRepository->save($devis);
	}

}