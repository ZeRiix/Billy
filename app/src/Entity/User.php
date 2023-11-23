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
use App\Entity\Organisation;
use App\Entity\Service;
use App\Entity\Facture;
use App\Entity\Role;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
	private ?Uuid $id;


	#[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
	#[ORM\JoinColumn(nullable: true)]
	private Collection $roles;

	#[ORM\ManyToMany(targetEntity: Organisation::class, inversedBy: 'users')]
	#[ORM\JoinColumn(nullable: true)]
	private Collection $organisations;

	#[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'user')]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $services;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'user')]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $factures;

	#[ORM\OneToMany(targetEntity: Organisation::class, mappedBy: 'createdBy')]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $createdOrganisations;

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
		$this->organisations = new ArrayCollection();
		$this->services = new ArrayCollection();
		$this->factures = new ArrayCollection();
		$this->createdOrganisations = new ArrayCollection();
	}

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getRoles(): Collection
	{
		return $this->roles;
	}

	public function setRoles(Collection $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	public function getOrganisations(): Collection
	{
		return $this->organisations;
	}

	public function setOrganisations(Collection $organisations): self
	{
		$this->organisations = $organisations;

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

	public function getCreatedOrganisations(): Collection
	{
		return $this->createdOrganisations;
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