<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\FactureRepository;
use App\Entity\Organization;
use App\Entity\Client;
use App\Entity\Devis;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

enum FactureStatus: string
{
	case WAITING = "waiting";
	case PAID = "payé";
}

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ORM\Table(name: "`facture`")]
#[ORM\HasLifecycleCallbacks]
class Facture
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[ORM\Column(type: Types::INTEGER, nullable: false)]
	private ?int $chrono = null;

	#[ORM\ManyToOne(inversedBy: "factures")]
	private ?Organization $organization = null;

	#[ORM\ManyToOne(targetEntity: Client::class, inversedBy: "factures")]
	#[ORM\JoinColumn(nullable: false)]
	private Client $client;

	#[ORM\OneToMany(targetEntity: Commande::class, mappedBy: "facture")]
	private Collection $commandes;

	#[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: "factures")]
	#[ORM\JoinColumn(nullable: false)]
	private Devis $devis;

	#[ORM\Column(type: Types::STRING, enumType: FactureStatus::class)]
	private FactureStatus $statut = FactureStatus::WAITING;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

	#[ORM\OneToMany(targetEntity: BillReminder::class, mappedBy: "facture")]
	private Collection $billReminders;

	public function __construct()
	{
		$this->commandes = new ArrayCollection();
		$this->billReminders = new ArrayCollection();
	}

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getChrono(): ?int
	{
		return $this->chrono;
	}

	public function setChrono(int $chrono): self
	{
		$this->chrono = $chrono;

		return $this;
	}

	public function getCommandes(): Collection
	{
		return $this->commandes;
	}

	public function addCommande(Commande $commande): self
	{
		if (!$this->commandes->contains($commande)) {
			$this->commandes[] = $commande;
			$commande->setFacture($this);
		}

		return $this;
	}

	public function getOrganization(): ?Organization
	{
		return $this->organization;
	}

	public function setOrganization(?Organization $organization): self
	{
		$this->organization = $organization;

		return $this;
	}

	public function getClient(): ?Client
	{
		return $this->client;
	}

	public function setClient(Client $client): self
	{
		$this->client = $client;

		return $this;
	}

	public function getDevis(): ?Devis
	{
		return $this->devis;
	}

	public function setDevis(Devis $devis): self
	{
		$this->devis = $devis;

		return $this;
	}

	public function getStatut(): FactureStatus
	{
		return $this->statut;
	}

	public function setStatut(FactureStatus $statut): static
	{
		$this->statut = $statut;

		return $this;
	}

	/**
	 * @return Collection<int, BillReminder>
	 */
	public function getBillReminders(): Collection
	{
		return $this->billReminders;
	}

	public function addBillReminder(BillReminder $billReminder): static
	{
		if (!$this->billReminders->contains($billReminder)) {
			$this->billReminders->add($billReminder);
			$billReminder->setFacture($this);
		}

		return $this;
	}

	public function removeBillReminder(BillReminder $billReminder): static
	{
		if ($this->billReminders->removeElement($billReminder)) {
			// set the owning side to null (unless already changed)
		}

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	#[ORM\PrePersist]
	public function setCreatedAt(): void
	{
		$this->created_at = new \DateTimeImmutable();
	}

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updated_at;
	}

	#[ORM\PrePersist]
	#[ORM\PreUpdate]
	public function setUpdatedAt(): void
	{
		$this->updated_at = new \DateTimeImmutable();
	}
}
