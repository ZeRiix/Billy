<?php

namespace App\Services\BillReminder;

use App\Entity\BillReminder;
use App\Entity\Facture;
use App\Repository\BillReminderRepository;
use App\Services\MailService;

class BillReminderService
{
	private BillReminderRepository $billReminderRepository;

	public function __construct(BillReminderRepository $billReminderRepository)
	{
		$this->billReminderRepository = $billReminderRepository;
	}

	public function create(BillReminder $billReminder, Facture $facture): void
	{
		$currentDate = new \DateTimeImmutable("now");
		$interval = $currentDate->diff($billReminder->getDateSend());
		if ($interval->invert != 0 || $interval->days > 30) {
			throw new \Exception("La date d'envoi n'est pas conforme.");
		}

		$remindersForFacture = $this->billReminderRepository->findBy(["facture" => $facture]);

		if (count($remindersForFacture) == 3) {
			throw new \Exception("Vous ne pouvez pas créer plus de 3 rappels pour une facture.");
		}

		$duplicates = $this->billReminderRepository->findBy([
			"facture" => $facture,
			"date_send" => $billReminder->getDateSend(),
		]);

		if (count($duplicates) > 0) {
			throw new \Exception("Un rappel pour cette date d'envoi existe déjà.");
		}

		$billReminder->setFacture($facture);
		$this->billReminderRepository->save($billReminder);
	}

	public function sendReminderBill(Facture $facture): void
	{
		$client = $facture->getClient();
		$organization = $facture->getOrganization();
		$devis = $facture->getDevis();

		if (!$client) {
			throw new \Exception(
				"Aucun client n'a été sélectionné pour la facture N°" .
					$facture->getChrono() .
					" du devis " .
					$devis->getName() .
					"."
			);
		}
		if (!$client->getEmail()) {
			throw new \Exception(
				"Aucune adresse mail à été trouvé pour le client " .
					$client->getName() .
					" " .
					$client->getFirstname() .
					"."
			);
		}

		$body = MailService::createHtmlBodyWithTwig("facture/rappel_email.html.twig", [
			"facture" => $facture->getChrono(),
			"devis" => $devis->getName(),
			"client" => $client->getName() . " " . $client->getFirstname(),
			"organization" => $organization,
		]);
		MailService::send(
			$client->getEmail(),
			"Rappel de paiement de la facture N°" . $facture->getChrono(),
			$body,
			false
		);
	}

	public function deleteAllRemindersForFacture(Facture $facture): void
	{
		$remindersForFacture = $this->billReminderRepository->findBy(["facture" => $facture]);
		foreach ($remindersForFacture as $reminder) {
			$this->billReminderRepository->delete($reminder);
		}
	}
}
