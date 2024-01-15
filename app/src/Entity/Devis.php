<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\DevisRepository;
use App\Entity\Organization;
use App\Entity\Service;
use App\Entity\Facture;
use App\Entity\Client;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
#[ORM\Table(name: "`devis`")]
#[ORM\HasLifecycleCallbacks]
class Devis
{
	#[ORM\Id]
   	#[ORM\Column(type: UuidType::NAME, unique: true)]
   	#[ORM\GeneratedValue(strategy: "CUSTOM")]
   	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
   	private ?Uuid $id;

	#[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Organization $organization = null;

	#[ORM\OneToMany(targetEntity: Service::class, mappedBy: "devis")]
   	#[ORM\JoinColumn(nullable: false)]
   	private Collection $services;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "devis")]
   	private Collection $factures;

	#[ORM\ManyToOne(targetEntity: Client::class, inversedBy: "devis")]
   	#[ORM\JoinColumn(nullable: false)]
   	private Client $client;

	#[ORM\Column(type: Types::TEXT)]
   	private ?string $num_devis = null;

	#[ORM\Column(length: 100)]
   	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT)]
   	private ?string $description = null;

	#[ORM\Column(type: Types::BOOLEAN)]
   	private ?bool $is_signed = null;

	#[
   		ORM\Column(
   			type: Types::DECIMAL,
   			nullable: false,
   			precision: 10,
   			scale: 2
   		)
   	]
   	private ?string $total_ht = null;

	#[
   		ORM\Column(
   			type: Types::DECIMAL,
   			nullable: false,
   			precision: 10,
   			scale: 2
   		)
   	]
   	private ?string $total_ttc = null;

	#[
   		ORM\Column(
   			type: Types::DECIMAL,
   			nullable: false,
   			precision: 10,
   			scale: 2
   		)
   	]
   	private $discount = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
   	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
   	private ?\DateTimeImmutable $updated_at = null;

	public function __construct()
   	{
   		$this->services = new ArrayCollection();
   		$this->factures = new ArrayCollection();
   	}

	public function getId(): ?Uuid
   	{
   		return $this->id;
   	}

	public function getOrganization(): ?Organization
   	{
   		return $this->Organization;
   	}

	public function setOrganization(?Organization $Organization): self
   	{
   		$this->Organization = $Organization;
   
   		return $this;
   	}

	public function getServices(): Collection
   	{
   		return $this->services;
   	}

	public function getFactures(): Collection
   	{
   		return $this->factures;
   	}

	public function getClient(): ?Client
   	{
   		return $this->client;
   	}

	public function setClient(?Client $client): self
   	{
   		$this->client = $client;
   
   		return $this;
   	}

	public function getNumDevis(): ?string
   	{
   		return $this->num_devis;
   	}

	public function setNumDevis(?string $num_devis): self
   	{
   		$this->num_devis = $num_devis;
   
   		return $this;
   	}

	public function getName(): ?string
   	{
   		return $this->name;
   	}

	public function setName(?string $name): static
   	{
   		$this->name = $name;
   
   		return $this;
   	}

	public function getDescription(): ?string
   	{
   		return $this->description;
   	}

	public function setDescription(?string $description): static
   	{
   		$this->description = $description;
   
   		return $this;
   	}

	public function getIsSigned(): ?bool
   	{
   		return $this->is_signed;
   	}

	public function setIsSigned(?bool $is_signed): static
   	{
   		$this->is_signed = $is_signed;
   
   		return $this;
   	}

	public function getTotalHt(): ?string
   	{
   		return $this->total_ht;
   	}

	public function setTotalHt(?string $total_ht): static
   	{
   		$this->total_ht = $total_ht;
   
   		return $this;
   	}

	public function getTotalTtc(): ?string
   	{
   		return $this->total_ttc;
   	}

	public function setTotalTtc(?string $total_ttc): static
   	{
   		$this->total_ttc = $total_ttc;
   
   		return $this;
   	}

	public function getDiscount(): ?string
   	{
   		return $this->discount;
   	}

	public function setDiscount(?string $discount): static
   	{
   		$this->discount = $discount;
   
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
