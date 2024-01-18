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
		$this->devisRepository->save($devis);
	}

}