<?php

namespace App\Services\Facture;

use App\Entity\Client;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Organization;
use App\Repository\CommandeRepository;
use App\Repository\FactureRepository;

class FactureService
{
	private FactureRepository $factureRepository;
	private CommandeRepository $commandeRepository;

	public function __construct(FactureRepository $factureRepository, CommandeRepository $commandeRepository)
	{
		$this->factureRepository = $factureRepository;
		$this->commandeRepository = $commandeRepository;
	}

	public function create(
		Facture $facture,
		Organization $organization,
		Client $client,
		Devis $devis,
		array $commandeIds
	) {
		$arrayKeys = array_keys($commandeIds);
		$commands = [];
		foreach ($arrayKeys as $commandeId) {
			$commande = $this->commandeRepository->find($commandeId);
			if ($commande === null) {
				throw new \Exception("La commande avec l'ID $commandeId n'existe pas.");
			}
			if ($commande->getFacture() !== null) {
				throw new \Exception("La commande avec l'ID $commandeId a déjà une facture.");
			}
			$commands[] = $commande;
		}

		if (empty($commands)) {
			throw new \Exception("Aucune commande n'a été sélectionnée.");
		}

		$facture->setOrganization($organization);
		$facture->setClient($client);
		$facture->setDevis($devis);
		$this->factureRepository->save($facture);
		foreach ($commands as $commande) {
			$commande->setFacture($facture);
			$this->commandeRepository->save($commande);
		}
	}
}
