<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\OrganisationRepository;
use App\Entity\Role;
use App\Entity\Service;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Client;
use App\Entity\User;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[ORM\Table(name: '`organisation`')]
#[ORM\HasLifecycleCallbacks]
class Organisation
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
	private ?Uuid $id;

	#[ORM\OneToMany(targetEntity: Role::class, mappedBy: 'organisation')]
	private Collection $roles;

	#[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'organisation')]
	private Collection $services;

	#[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'organisation')]
	private Collection $devis;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'organisation')]
	private Collection $factures;

	#[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'organisation')]
	private Collection $clients;

	#[ORM\ManyToMany(targetEntity: Organisation::class, inversedBy: 'organisations')]
	#[ORM\JoinColumn(nullable: true)]
	private Collection $users;

	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdOrganisations')]
	#[ORM\JoinColumn(nullable: false)]
	private User $createdBy;

	#[ORM\Column(length: 100, nullable: false)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $adress = null;

	#[ORM\Column(length: 255)]
	private ?string $email = null;

	#[ORM\Column(length: 20)]
	private ?string $phone = null;

	#[ORM\Column(length: 100)]
	private ?string $activity = null;

	#[ORM\Column(length: 14, nullable: false)]
	private ?string $siret = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

	public function __construct()
	{
		$this->roles = new ArrayCollection();
		$this->services = new ArrayCollection();
		$this->devis = new ArrayCollection();
		$this->factures = new ArrayCollection();
		$this->clients = new ArrayCollection();
		$this->users = new ArrayCollection();
	}

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getRoles(): Collection
	{
		return $this->roles;
	}

	public function getServices(): Collection
	{
		return $this->services;
	}

	public function getDevis(): Collection
	{
		return $this->devis;
	}

	public function getFactures(): Collection
	{
		return $this->factures;
	}

	public function getClients(): Collection
	{
		return $this->clients;
	}

	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function getCreatedBy(): ?User
	{
		return $this->createdBy;
	}

	public function setCreatedBy(?User $createdBy): self
	{
		$this->createdBy = $createdBy;

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

	public function getSiret(): ?string
	{
		return $this->siret;
	}

	public function setSiret(?string $siret): static
	{
		$this->siret = $siret;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	#[ORM\PrePersist]
	public function setCreatedAtValue(): void
	{
		$this->created_at = new \DateTimeImmutable();
	}

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updated_at;
	}

	#[ORM\PrePersist]
	#[ORM\PreUpdate]
	public function setUpdatedAtValue(): void
	{
		$this->updated_at = new \DateTimeImmutable();
	}
}