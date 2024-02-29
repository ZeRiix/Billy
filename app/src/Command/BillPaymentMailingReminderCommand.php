<?php

namespace App\Command;

use App\Entity\Facture;
use App\Repository\BillReminderRepository;
use App\Services\BillReminder\BillReminderService;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[
	AsCommand(
		name: "app:bill-payment-mailing-reminder",
		description: 'Système d\'envoie de rappels de paiment des factures par mail. '
	)
]
class BillPaymentMailingReminderCommand extends Command
{
	private BillReminderRepository $billReminderRepository;
	private BillReminderService $billReminderService;

	public function __construct(
		BillReminderRepository $billReminderRepository,
		BillReminderService $billReminderService
	) {
		parent::__construct();
		$this->billReminderRepository = $billReminderRepository;
		$this->billReminderService = $billReminderService;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$currentDate = new DateTimeImmutable("now");
		$reminders = $this->billReminderRepository->findAll();
		$mailCount = 0;

		foreach ($reminders as $reminder) {
			$interval = $currentDate->diff($reminder->getDateSend());
			if ($interval->invert != 0) {
				$this->billReminderService->sendReminderBill($reminder->getFacture());
				$this->billReminderRepository->delete($reminder);
				$mailCount++;
			}
		}

		$io->success("Nombre de mail(s) envoyé(s) : " . $mailCount . ".");

		return Command::SUCCESS;
	}
}
