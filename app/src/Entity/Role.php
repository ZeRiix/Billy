<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

//local imports
use App\Repository\RoleRepository;
use App\Entity\Organization;
use App\Entity\User;

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
	private ?string $name = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	private ?bool $manage_org = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	private ?bool $manage_user = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	private ?bool $manage_client = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	private ?bool $write_devis = null;

	#[ORM\Column(type: Types::BOOLEAN)]
	private ?bool $write_factures = null;

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

	public function getWriteFactures(): ?bool
	{
		return $this->write_factures;
	}

	public function setWriteFactures(?bool $write_factures): static
	{
		$this->write_factures = $write_factures;

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
