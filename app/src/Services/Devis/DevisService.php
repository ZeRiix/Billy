<?php 

namespace App\Services\Devis;

use App\Entity\Devis;
use App\Entity\DeviStatus;
use App\Entity\Organization;
use App\Repository\DevisRepository;

class DevisService {

	private DevisRepository $devisRepository;

	public function __construct(
		DevisRepository $devisRepository
	) {
		$this->devisRepository = $devisRepository;
	}

	public function create(Devis $devis, Organization $organization) : void 
	{
		$devis->setOrganization($organization);
		$this->devisRepository->save($devis);
	}

	public function update(Devis $devis) : void 
	{
		if ($devis->getDiscount() < 0 || $devis->getDiscount() > 100) {
			throw new \Exception("Le taux de remise doit être compris entre 0 et 100.");
		} else if ($devis->getStatus() !== DeviStatus::EDITING) {
			throw new \Exception("Le devis est verrouillé ou bien déjà signé, vous ne pouvez pas le modifier.");
		}
		$this->devisRepository->save($devis);
	}

	public function findById(int $id) : bool 
	{
		$devis = $this->devisRepository->find($id);
		if(!$devis) return false;
		return true;
	}

	public function devisBelongsToOrganization(Devis $devis, Organization $organization) : bool 
	{
		if($devis->getOrganization() !== $organization) return false;
		return true;
	}

}