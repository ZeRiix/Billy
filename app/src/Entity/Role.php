<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

//local imports
use App\Repository\RoleRepository;
use App\Entity\Organization;
use App\Entity\User;
use phpDocumentor\Reflection\Types\Boolean;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: "`role`")]
#[ORM\HasLifecycleCallbacks]
class Role
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[ORM\ManyToOne(targetEntity: Organization::class, inversedBy: "roles", cascade: ["persist"])]
	#[ORM\JoinColumn(nullable: false)]
	private Organization $organization;

	#[ORM\ManyToMany(targetEntity: User::class, mappedBy: "roles")]
	#[ORM\JoinColumn(nullable: true)]
	#[ORM\JoinTable(name: "user_role")]
	private ?Collection $users;

	#[ORM\Column(length: 100, nullable: false)]
	#[Assert\NotBlank(message: "Veuillez renseigner le nom du rôle.")]
	#[
		Assert\Length(
			min: 4,
			max: 100,
			minMessage: "Le nom du rôle doit contenir au moins {{ limit }} caractères.",
			maxMessage: "Le nom du rôle doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $name = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $manage_org = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $manage_user = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $manage_client = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $write_devis = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $read_devis = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $write_factures = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $read_factures = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	#[Assert\Type(type: "bool")]
	private ?bool $manage_service = null;

	#[ORM\Column(type: Types::BOOLEAN, options: ["default" => false])]
	#[Assert\Type(type: "bool")]
	private ?bool $view_stats = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

	public function __construct()
	{
		$this->users = new ArrayCollection();
	}

	public function getId(): ?Uuid
	{
		return $this->id;
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

	public function getUsers(): ?Collection
	{
		return $this->users;
	}

	public function addUser(User $user)
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
			$user->addRole($this);
		}
	}

	public function removeUser(User $user)
	{
		if ($this->users->contains($user)) {
			$this->users->removeElement($user);
			$user->removeRole($this);
		}
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

	public function getManageOrg(): ?bool
	{
		return $this->manage_org;
	}

	public function setManageOrg(?bool $manage_org): static
	{
		$this->manage_org = $manage_org;

		return $this;
	}

	public function getManageUser(): ?bool
	{
		return $this->manage_user;
	}

	public function setManageUser(?bool $manage_user): static
	{
		$this->manage_user = $manage_user;

		return $this;
	}

	public function getManageClient(): ?bool
	{
		return $this->manage_client;
	}

	public function setManageClient(?bool $manage_client): static
	{
		$this->manage_client = $manage_client;

		return $this;
	}

	public function getWriteDevis(): ?bool
	{
		return $this->write_devis;
	}

	public function setWriteDevis(?bool $write_devis): static
	{
		$this->write_devis = $write_devis;

		return $this;
	}

	public function getReadDevis(): ?bool
	{
		return $this->read_devis;
	}

	public function setReadDevis(?bool $read_devis): static
	{
		$this->read_devis = $read_devis;

		return $this;
	}

	public function getWriteFactures(): ?bool
	{
		return $this->write_factures;
	}

	public function setWriteFactures(?bool $write_factures): static
	{
		$this->write_factures = $write_factures;

		return $this;
	}

	public function getReadFactures(): ?bool
	{
		return $this->read_factures;
	}

	public function setReadFactures(?bool $read_factures): static
	{
		$this->read_factures = $read_factures;

		return $this;
	}

	public function getManageService(): ?bool
	{
		return $this->manage_service;
	}

	public function setManageService(?bool $manage_service): static
	{
		$this->manage_service = $manage_service;

		return $this;
	}

	public function getViewStats(): ?bool
	{
		return $this->view_stats;
	}

	public function setViewStats(?bool $view_stats): static
	{
		$this->view_stats = $view_stats;

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
