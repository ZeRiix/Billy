<?php

namespace App\Services\Facture;

use App\Entity\Client;
use App\Entity\Devis;
use App\Entity\DeviStatus;
use App\Entity\Facture;
use App\Entity\Organization;
use App\Repository\CommandeRepository;
use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use App\Services\MailService;
use Doctrine\Common\Collections\Collection;

class FactureService
{
	public function __construct(
		private FactureRepository $factureRepository,
		private CommandeRepository $commandeRepository,
		private DevisRepository $devisRepository
	) {
	}

	public function create(
		Facture $facture,
		Organization $organization,
		Client $client,
		Devis $devis,
		array $commandeIds
	): void {
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
		$facture->setChrono($this->factureRepository->genChrono($organization));
		$this->factureRepository->save($facture);
		foreach ($commands as $commande) {
			$commande->setFacture($facture);
			$this->commandeRepository->save($commande);
		}
		$this->checkIfDevisIsCompleted($devis->getCommandes());
		$this->sendBill($facture);
	}

	public function sendBill(Facture $bill): void
	{
		if (!$bill->getClient()) {
			throw new \Exception("Vous n'avez pas sélectionné de client pour cette facture.");
		}
		$client = $bill->getClient();
		if (!$client->getEmail()) {
			throw new \Exception("Le client sélectionné n'a pas d'adresse email.");
		}
		// make mail body
		$body = MailService::createHtmlBodyWithTwig("facture/email.html.twig", [
			"facture" =>
				getenv("ORIGIN") .
				"/organization/" .
				$bill->getOrganization()->getId() .
				"/quotation/" .
				$bill->getDevis()->getId() .
				"/bill/" .
				$bill->getId() .
				"/preview",
			"name" => $client->getName() . " " . $client->getFirstname(),
			"organization" => $bill->getOrganization(),
		]);
		// send mail
		MailService::send($client->getEmail(), "Facture N°" . $bill->getChrono(), $body, false);
	}

	public function checkIfDevisIsCompleted(Collection $commandes)
	{
		$isCompleted = true;
		foreach ($commandes as $commande) {
			if ($commande->getFacture() === null) {
				$isCompleted = false;
				break;
			}
		}
		if ($isCompleted) {
			$devis = $commandes->first()->getDevis();
			$devis->setStatus(DeviStatus::COMPLETED);
			$this->devisRepository->save($devis);
		}
	}
}
