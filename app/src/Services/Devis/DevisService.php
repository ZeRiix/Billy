<?php

namespace App\Services\Devis;

use App\Entity\Devis;
use App\Entity\DeviStatus;
use App\Entity\Organization;
use App\Repository\DevisRepository;
use App\Services\MailService;
use Error;

class DevisService
{
	private DevisRepository $devisRepository;

	public function __construct(DevisRepository $devisRepository)
	{
		$this->devisRepository = $devisRepository;
	}

	public function create(Devis $devis, Organization $organization): void
	{
		$devis->setOrganization($organization);
		$this->devisRepository->save($devis);
	}

	public function update(Devis $devis): void
	{
		if ($devis->getDiscount() < 0 || $devis->getDiscount() > 100) {
			throw new \Exception("Le taux de remise doit être compris entre 0 et 100.");
		}
		if ($devis->getStatus() !== DeviStatus::EDITING) {
			throw new \Exception(
				"Le devis est verrouillé ou bien déjà signé, vous ne pouvez pas le modifier."
			);
		}
		$this->devisRepository->save($devis);
	}

	public function findById(int $id): bool
	{
		$devis = $this->devisRepository->find($id);
		if (!$devis) {
			return false;
		}
		return true;
	}

	public function devisBelongsToOrganization(Devis $devis, Organization $organization): bool
	{
		if ($devis->getOrganization() !== $organization) {
			return false;
		}
		return true;
	}

	public function sendDevis(Devis $devis)
	{
		if ($devis->getStatus() !== DeviStatus::EDITING) {
			throw new \Exception("Vous ne pouvez pas envoyer un devis vérouiller.");
		}

		if (!isset($devis->getCommandes()[0])) {
			throw new \Exception("Il faut minimum une commande pour envoyer un devis.");
		}

		$client = $devis->getClient();

		if (!$client) {
			throw new \Exception("Vous n'avez pas selectioner de client pour ce devis.");
		}

		$email = $client->getEmail();

		if (!$email) {
			throw new \Exception("Le client selectioner na pas d'adresse email.");
		}

		$devis->setStatus(DeviStatus::LOCK);

		$mail = new MailService();
		$mail->send(
			$email,
			"preview devis",
			"/organization/" .
				$devis->getOrganization()->getId() .
				"/quotation/" .
				$devis->getId() .
				"/preview",
			false
		);

		$this->devisRepository->save($devis);
	}
}
