<?php

namespace App\Services\Commande;

use App\Repository\CommandeRepository;
use App\Entity\Commande;
use App\Entity\Devis;
use App\Repository\DevisRepository;

class CommandeService
{
	private DevisRepository $devisRepository;
	private CommandeRepository $commandeRepository;

	public function __construct(DevisRepository $devisRepository, CommandeRepository $commandeRepository)
	{
		$this->devisRepository = $devisRepository;
		$this->commandeRepository = $commandeRepository;
	}

	public function create(Commande $commande, Devis $devis): void
	{
		$this->updateMontantForCommande($commande);
		$devis->addCommande($commande);
		$this->devisRepository->save($commande);
	}

	public function update(Commande $commande): void
	{
		$this->updateMontantForCommande($commande);
		$this->devisRepository->save($commande);
	}

	private function updateMontantForCommande(Commande $commande) : void
	{
		$commande->setMontant($commande->getQuantity() * $commande->getUnitPrice());
	}

	public function delete(Commande $commande): void
	{
		$this->commandeRepository->delete($commande);
	}
}