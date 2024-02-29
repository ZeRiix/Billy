<?php

namespace App\Entity;

use App\Repository\BillReminderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillReminderRepository::class)]
class BillReminder
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(targetEntity: Facture::class, inversedBy: "billReminders")]
	private Facture $facture;

	#[ORM\Column(type: Types::DATE_IMMUTABLE)]
	private \DateTimeImmutable $date_send;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getDateSend(): \DateTimeImmutable
	{
		return $this->date_send;
	}

	public function setDateSend(\DateTimeImmutable $date_send): static
	{
		$this->date_send = $date_send;

		return $this;
	}

	public function getFacture(): Facture
	{
		return $this->facture;
	}

	public function setFacture(Facture $facture): static
	{
		$this->facture = $facture;

		return $this;
	}
}
