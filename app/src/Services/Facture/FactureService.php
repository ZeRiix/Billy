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
		$mail = new MailService();
		//Entity
		$devis = $bill->getDevis();
		$organization = $bill->getOrganization();
		$client = $bill->getClient();
		//Entity Ids
		$organizationId = $organization->getId();
		$devisId = $devis->getId();
		$factureId = $bill->getId();

		if (!$client) {
			throw new \Exception("Vous n'avez pas sélectionné de client pour cette facture.");
		}

		$email = $client->getEmail();

		if (!$email) {
			throw new \Exception("Le client sélectionné n'a pas d'adresse email.");
		}

		$subject = "Facture N°" . $bill->getChrono();
		$htmlContent =
			"Bonjour " .
			$client->getName() .
			" " .
			$client->getFirstname() .
			",<br><br>" .
			"Vous trouverez votre facture en cliquant sur ce lien : <a href='http://localhost:8000/organization/$organizationId/quotation/$devisId/bill/$factureId/preview'>Facture N°" .
			$bill->getChrono() .
			"</a>.<br><br>" .
			"Cordialement,<br><br>" .
			$organization->getName() .
			".";
		$mail->send($email, $subject, $htmlContent, false);
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
