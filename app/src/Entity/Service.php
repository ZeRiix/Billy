<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

// local imports
use App\Repository\ServiceRepository;
use App\Entity\Organization;
use App\Entity\Commande;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: "`service`")]
#[ORM\HasLifecycleCallbacks]
class Service
{
	#[Groups(["service"])]
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[Groups(["organization"])]
	#[ORM\ManyToOne(inversedBy: "services")]
	private ?Organization $organization = null;

	#[Groups(["commandes"])]
	#[ORM\OneToMany(targetEntity: Commande::class, mappedBy: "service")]
	private Collection $commandes;

	//add to late manyToMany devis
	#[Groups(["service"])]
	#[ORM\Column(length: 100)]
	#[Assert\NotBlank(message: "Veuillez renseigner le nom du service.")]
	#[
		Assert\Length(
			min: 3,
			max: 100,
			minMessage: "Le nom du service doit contenir au moins {{ limit }} caractères.",
			maxMessage: "Le nom du service doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $name = null;

	#[Groups(["service"])]
	#[ORM\Column(length: 1000)]
	#[
		Assert\Length(
			max: 1000,
			maxMessage: "La description du service doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $description = null;

	#[Groups(["service"])]
	#[ORM\Column(type: Types::FLOAT, nullable: true, precision: 10, scale: 2)]
	private ?string $unitPrice = null;

	#[Groups(["service"])]
	#[ORM\Column(type: Types::BOOLEAN)]
	private ?bool $isArchived = false;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

	public function __construct()
	{
		$this->commandes = new ArrayCollection();
	}

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getOrganization(): ?Organization
	{
		return $this->organization;
	}

	public function setOrganization(Organization $organization): self
	{
		$this->organization = $organization;

		return $this;
	}

	public function getCommandes(): Collection
	{
		return $this->commandes;
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

	public function getUnitPrice(): ?string
	{
		return $this->unitPrice;
	}

	public function setUnitPrice(?string $unitPrice): static
	{
		$this->unitPrice = $unitPrice;

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

	public function getIsArchived(): ?bool
	{
		return $this->isArchived;
	}

	public function setIsArchived(bool $isArchived): static
	{
		$this->isArchived = $isArchived;

		return $this;
	}
}
