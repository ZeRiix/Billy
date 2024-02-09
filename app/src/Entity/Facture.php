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

	#[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: "factures")]
	#[ORM\JoinColumn(nullable: false)]
	private Devis $devis;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $num_facture = null;

	#[ORM\Column(length: 100)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $description = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	private ?bool $is_signed = null;

	#[ORM\Column(type: Types::DECIMAL, nullable: false, precision: 10, scale: 2)]
	private ?string $total_ht = null;

	#[ORM\Column(type: Types::DECIMAL, nullable: false, precision: 10, scale: 2)]
	private ?string $total_ttc = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

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

	public function getNumFacture(): ?string
	{
		return $this->num_facture;
	}

	public function setNumFacture(string $num_facture): self
	{
		$this->num_facture = $num_facture;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(string $description): static
	{
		$this->description = $description;

		return $this;
	}

	public function getIsSigned(): ?bool
	{
		return $this->is_signed;
	}

	public function setIsSigned(bool $is_signed): static
	{
		$this->is_signed = $is_signed;

		return $this;
	}

	public function getTotalHt(): ?string
	{
		return $this->total_ht;
	}

	public function setTotalHt(string $total_ht): static
	{
		$this->total_ht = $total_ht;

		return $this;
	}

	public function getTotalTtc(): ?string
	{
		return $this->total_ttc;
	}

	public function setTotalTtc(string $total_ttc): static
	{
		$this->total_ttc = $total_ttc;

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
