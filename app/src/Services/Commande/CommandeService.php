<?php

namespace App\Services\Commande;

use App\Repository\CommandeRepository;
use App\Entity\Commande;
use App\Entity\Devis;
use App\Repository\DevisRepository;

class CommandeService
{
	private DevisRepository $devisRepository;

	public function __construct(DevisRepository $devisRepository)
	{
		$this->devisRepository = $devisRepository;
	}

	public function create(Commande $commande, Devis $devis): void
	{
		$devis->addCommande($commande);
		$this->devisRepository->save($commande);
	}

	public function update(Commande $commande): void
	{
		$this->devisRepository->save($commande);
	}
}