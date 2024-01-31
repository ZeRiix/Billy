<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\CommandeRepository;
use App\Entity\Service;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: '`commande`')]
#[ORM\HasLifecycleCallbacks]
class Commande
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
	private ?Uuid $id;

	#[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'commandes')]
	#[ORM\JoinColumn(nullable: false)]
	private Service $service;

	#[ORM\Column(length: 100)]
	#[Assert\NotBlank(message: 'Veuillez renseigner le nom de la commande.')]
	#[Assert\Length(
		min: 3,
		max: 100,
		minMessage: 'Le nom de la commande doit contenir au moins {{ limit }} caractères.',
		maxMessage: 'Le nom de la commande doit contenir au maximum {{ limit }} caractères.'
	)]
	private ?string $name = null;

	#[ORM\Column(length: 1000)]
   	#[Assert\Length(
   		max: 1000,
   		maxMessage: "La description de la commande doit contenir au maximum {{ limit }} caractères."
   	)]
	private ?string $description = null;

	#[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
   	private float $unitPrice;

	#[ORM\Column(type: Types::INTEGER, precision: 10, scale: 2)]
   	private int $quantity;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devis $devis;

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getService(): ?Service
	{
		return $this->service;
	}

	public function setService(Service $service): self
	{
		$this->service = $service;
		
		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		
		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;
         
		return $this;
	}

	public function getUnitPrice(): ?float
   	{
   		return $this->unitPrice;
   	}

	public function setUnitPrice(?float $unitPrice): static
   	{
   		$this->unitPrice = $unitPrice;
   
   		return $this;
   	}

	public function getQuantity(): ?int
   	{
   		return $this->quantity;
   	}

	public function setQuantity(?int $quantity): static
   	{
   		$this->quantity = $quantity;
   
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

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }
}