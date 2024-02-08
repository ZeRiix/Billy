<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\ClientRepository;
use App\Entity\Organization;
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

	#[ORM\ManyToOne(inversedBy: "clients")]
	private ?Organization $organization = null;

	#[ORM\OneToMany(targetEntity: Devis::class, mappedBy: "client")]
	private Collection $devis;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "client")]
	private Collection $factures;

	#[ORM\Column(length: 100, nullable: false)]
	#[Assert\NotBlank(["allowNull" => true], message: "Veuillez renseigner le nom du client.")]
	#[
		Assert\Length(
			min: 4,
			max: 100,
			minMessage: "Le nom du client doit contenir au moins {{ limit }} caractères.",
			maxMessage: "Le nom du client doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $name = null;

	#[ORM\Column(length: 100, nullable: true)]
	#[Assert\NotBlank(["allowNull" => true], message: "Veuillez renseigner le prénom du client.")]
	#[
		Assert\Length(
			min: 4,
			max: 100,
			minMessage: "Le prénom du client doit contenir au moins {{ limit }} caractères.",
			maxMessage: "Le prénom du client doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $firstname = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	#[Assert\NotBlank(["allowNull" => true], message: "Veuillez renseigner l'adresse du client.")]
	#[
		Assert\Length(
			min: 10,
			max: 255,
			minMessage: "L'adresse du client doit contenir au moins {{ limit }} caractères.",
			maxMessage: "L'adresse du client doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $address = null;

	#[ORM\Column(length: 320, nullable: false)]
	#[Assert\NotBlank(message: "Veuillez renseigner l'email du client.")]
	#[
		Assert\Length(
			min: 10,
			max: 320,
			minMessage: "L'email du client doit contenir au moins {{ limit }} caractères.",
			maxMessage: "L'email du client doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $email = null; //Pas testé si regex fonctionne à test quand on aura un form

	#[ORM\Column(length: 10)]
	#[Assert\NotBlank(message: "Veuillez renseigner le numéro de téléphone du client")]
	#[
		Assert\Regex(
			pattern: "/^0[1-9]([-. ]?[0-9]{2}){4}$/",
			message: "Veuillez renseigner un numéro de téléphone valide."
		)
	]
	#[
		Assert\Length(
			min: 10,
			max: 10,
			minMessage: "Le numéro de téléphone doit contenir au moins {{ limit }} caractères.",
			maxMessage: "Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères."
		)
	]
	private ?string $phone = null;

	#[ORM\Column(length: 50, nullable: true)]
	#[Assert\NotBlank(["allowNull" => true], message: "Veuillez renseigner l'activité du client.")]
	#[
		Assert\Length(
			min: 2,
			max: 50,
			minMessage: "L'activité du client doit contenir au moins {{ limit }} caractères.",
			maxMessage: "L'activité doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $activity = null;

	#[ORM\Column(length: 14, nullable: true, unique: true)]
	#[Assert\NotBlank(["allowNull" => true], message: "Veuillez renseigner le siret du client.")]
	#[Assert\Length(min: 14, max: 14, exactMessage: "Le siret doit contenir {{ limit }} caractères.")]
	private ?string $siret = null;

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
		return $this->organization;
	}

	public function setOrganization(?Organization $Organization): self
	{
		$this->organization = $Organization;

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

	public function setPhone(string $phone): static
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
