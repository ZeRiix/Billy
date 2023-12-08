<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\UserRepository;
use App\Entity\Organization;
use App\Entity\Service;
use App\Entity\Facture;
use App\Entity\Role;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[ORM\HasLifecycleCallbacks]
class User
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[ORM\ManyToMany(targetEntity: Role::class, inversedBy: "users")]
	#[ORM\JoinColumn(nullable: true)]
	#[ORM\JoinTable(name: "user_role")]
	private Collection $roles;

	#[ORM\ManyToMany(targetEntity: Organization::class, inversedBy: "users")]
	#[ORM\JoinColumn(nullable: true)]
	private Collection $Organizations;

	#[ORM\OneToMany(targetEntity: Service::class, mappedBy: "user")]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $services;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "user")]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $factures;

	#[ORM\OneToMany(targetEntity: Organization::class, mappedBy: "createdBy")]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $createdOrganizations;

	#[ORM\Column(length: 100, nullable: false)]
	private ?string $firstName = null;

	#[ORM\Column(length: 100, nullable: false)]
	private ?string $name = null;

	#[ORM\Column(length: 320, nullable: false)]
	private ?string $email = null;

	#[ORM\Column(type: Types::TEXT, nullable: false)]
	private ?string $password = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

	//methods

	public function __construct()
	{
		$this->roles = new ArrayCollection();
		$this->Organizations = new ArrayCollection();
		$this->services = new ArrayCollection();
		$this->factures = new ArrayCollection();
		$this->createdOrganizations = new ArrayCollection();
	}

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getRoles(): Collection
	{
		return $this->roles;
	}

	public function addRole(Role $role)
	{
		if (!$this->roles->contains($role)) {
			$this->roles->add($role);
			$role->addUser($this);
		}
	}

	public function removeRole(Role $role)
	{
		if ($this->roles->contains($role)) {
			$this->roles->removeElement($role);
			$role->removeUser($this);
		}
	}

	public function getOrganizations(): Collection
	{
		return $this->Organizations;
	}

	public function setOrganizations(Collection $Organizations): self
	{
		$this->Organizations = $Organizations;

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

	public function getCreatedOrganizations(): Collection
	{
		return $this->createdOrganizations;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;

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

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	#[ORM\PrePersist]
	public function setCreatedAt(): self
	{
		$this->created_at = new \DateTimeImmutable();

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updated_at;
	}

	#[ORM\PrePersist]
	#[ORM\PreUpdate]
	public function setUpdatedAt(): self
	{
		$this->updated_at = new \DateTimeImmutable();

		return $this;
	}
}
