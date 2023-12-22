<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

// local imports
use App\Repository\OrganizationRepository;
use App\Entity\Role;
use App\Entity\Service;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Client;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\Table(name: "`Organization`")]
#[ORM\HasLifecycleCallbacks]
class Organization
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[ORM\OneToMany(targetEntity: Role::class, mappedBy: "Organization")]
	private Collection $roles;

	#[ORM\OneToMany(targetEntity: Service::class, mappedBy: "Organization")]
	private Collection $services;

	#[ORM\OneToMany(targetEntity: Devis::class, mappedBy: "Organization")]
	private Collection $devis;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "Organization")]
	private Collection $factures;

	#[ORM\OneToMany(targetEntity: Client::class, mappedBy: "Organization")]
	private Collection $clients;

	#[ORM\OneToMany(targetEntity: InviteOrganization::class, mappedBy: "organization")]
	private Collection $invite_organizations;

	#[ORM\ManyToMany(targetEntity: User::class, inversedBy: "organizations")]
	#[ORM\JoinColumn(nullable: true)]
	#[ORM\JoinTable(name: "user_organizations")]
	private Collection $users;

	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: "createdOrganizations")]
	#[ORM\JoinColumn(nullable: false)]
	private User $createdBy;

	#[ORM\Column(length: 100, nullable: false)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $address = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank(message: "Veuillez renseigner l\'email de l\'organisation.")]
	#[Assert\Email(message: "Veuillez renseigner un email valide.")]
	#[Assert\Length(
		min: 5,
		max: 255,
		minMessage: "L\'email doit contenir au moins {{ limit }} caractères.",
		maxMessage: "L\'email ne peut pas dépasser {{ limit }} caractères."
	)]
	private ?string $email = null;

	#[ORM\Column(length: 10)]
	#[Assert\NotBlank(message: "Veuillez renseigner le numéro de téléphone de l\'organisation.")]
	#[Assert\Regex(
		pattern: "/^0[1-9]([-. ]?[0-9]{2}){4}$/", //Pas testé si regex fonctionne à test quand on aura un form
		message: "Veuillez renseigner un numéro de téléphone valide."
	)]
	#[Assert\Length(
		min: 10,
		max: 10,
		minMessage: "Le numéro de téléphone doit contenir au moins {{ limit }} caractères.",
		maxMessage: "Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères."
	)]
	private ?string $phone = null;

	#[ORM\Column(length: 100)]
	#[Assert\NotBlank(message: "Veuillez renseigner l'activité de votre organisation.")]
	#[Assert\Length(
		min: 2,
		max: 50,
		minMessage: "L'activité doit contenir au moins {{ limit }} caractères.",
		maxMessage: "L'activité ne peut pas dépasser {{ limit }} caractères."
	)]
	private ?string $activity = null;

	#[ORM\Column(length: 14, nullable: false, unique: true)]
	#[Assert\NotBlank(message: "Veuillez renseigner le siret de l'organisation.")]
	#[Assert\Length(
		min: 14,
		max: 14,
		exactMessage: "Le siret doit contenir {{ limit }} caractères."
	)]
	private ?string $siret = null;

	private ?UploadedFile $image = null;

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
		$this->invite_organizations = new ArrayCollection();
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

	public function getInviteOrganizations(): Collection
	{
		return $this->invite_organizations;
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

	public function addUser(User $user): self
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
		}

		return $this;
	}

	public function removeUser(User $user): self
	{
		if ($this->users->contains($user)) {
			$this->users->removeElement($user);
		}

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

	public function getAddress(): ?string
	{
		return $this->address;
	}

	public function setAddress(?string $address): static
	{
		$this->address = $address;

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

	public function getImage(): ?UploadedFile
	{
		return $this->image;
	}

	public function setImage(UploadedFile $image): self
	{
		$this->image = $image;

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