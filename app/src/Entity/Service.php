<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\ServiceRepository;
use App\Entity\Organisation;
use App\Entity\Commande;
use App\Entity\Devis;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: '`service`')]
#[ORM\HasLifecycleCallbacks]
class Service
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
	private ?Uuid $id;

	#[ORM\ManyToOne(targetEntity: Organisation::class, inversedBy: 'services')]
	#[ORM\JoinColumn(nullable: false)]
	private Organisation $organisation;

	#[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'service')]
	private Collection $commandes;

	#[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: 'services')]
	#[ORM\JoinColumn(nullable: false)]
	private Devis $devis;

	#[ORM\Column(length: 100)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $description = null;

	#[ORM\Column(type: Types::DECIMAL, nullable: false, precision: 10, scale: 2)]
	private ?string $total_ht = null;

	#[ORM\Column(type: Types::DECIMAL, nullable: false, precision: 10, scale: 2)]
	private ?string $total_ttc = null;

	#[ORM\Column(type: Types::DECIMAL, nullable: false, precision: 10, scale: 2)]
	private $discount = null;

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

	public function getOrganisation(): ?Organisation
	{
		return $this->organisation;
	}

	public function setOrganisation(?Organisation $organisation): self
	{
		$this->organisation = $organisation;

		return $this;
	}

	public function getCommandes(): Collection
	{
		return $this->commandes;
	}

	public function getDevis(): ?Devis
	{
		return $this->devis;
	}

	public function setDevis(?Devis $devis): self
	{
		$this->devis = $devis;

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