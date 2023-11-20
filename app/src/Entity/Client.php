<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\ClientRepository;
use App\Entity\Organiation;
use App\Entity\Devis;
use App\Entity\Facture;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: "`client`")]
#[ORM\HasLifecycleCallbacks]
class Client
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: "clients")]
	#[ORM\JoinColumn(nullable: false)]
	private Organization $Organization;

	#[ORM\OneToMany(targetEntity: Devis::class, mappedBy: "client")]
	private Collection $devis;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "client")]
	private Collection $factures;

	#[ORM\Column(length: 100, nullable: false)]
	private ?string $name = null;

	#[ORM\Column(length: 100, nullable: false)]
	private ?string $firstname = null;

	#[ORM\Column(type: Types::TEXT, nullable: false)]
	private ?string $adress = null;

	#[ORM\Column(length: 255, nullable: false)]
	private ?string $email = null;

	#[ORM\Column(length: 20, nullable: false)]
	private ?string $phone = null;

	#[ORM\Column(length: 100, nullable: false)]
	private ?string $activity = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

	public function __construct()
	{
		$this->devis = new ArrayCollection();
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

	public function getDevis(): Collection
	{
		return $this->devis;
	}

	public function getFactures(): Collection
	{
		return $this->factures;
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

	public function getFirstname(): ?string
	{
		return $this->firstname;
	}

	public function setFirstname(?string $firstname): static
	{
		$this->firstname = $firstname;

		return $this;
	}

	public function getAdress(): ?string
	{
		return $this->adress;
	}

	public function setAdress(?string $adress): static
	{
		$this->adress = $adress;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(?string $email): static
	{
		$this->email = $email;

		return $this;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): static
	{
		$this->phone = $phone;

		return $this;
	}

	public function getActivity(): ?string
	{
		return $this->activity;
	}

	public function setActivity(?string $activity): static
	{
		$this->activity = $activity;

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
