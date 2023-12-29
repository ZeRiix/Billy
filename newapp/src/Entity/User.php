<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà utilisée.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[ORM\ManyToMany(targetEntity: Role::class, inversedBy: "users")]
	#[ORM\JoinColumn(nullable: true)]
	#[ORM\JoinTable(name: "user_role")]
	private Collection $userRoles;

	#[ORM\ManyToMany(targetEntity: Organization::class, mappedBy: "users")]
	#[ORM\JoinColumn(nullable: true)]
	#[ORM\JoinTable(name: "user_organizations")]
	private Collection $organizations;

	#[ORM\OneToMany(targetEntity: Service::class, mappedBy: "user")]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $services;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "user")]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $factures;

	#[ORM\OneToMany(targetEntity: Organization::class, mappedBy: "createdBy")]
	#[ORM\JoinColumn(nullable: false)]
	private Collection $createdOrganizations;

	#[ORM\OneToMany(targetEntity: InviteOrganization::class, mappedBy: "user")]
	private Collection $invite_users;

    #[ORM\Column(length: 320, unique: true)]
	#[Assert\NotBlank(message: "Veuillez renseigner l'adresse email.")]
	#[Assert\Length(
         		min: 10,
         		max: 320,
         		minMessage: "L'adresse email doit contenir au moins {{ limit }} caractères.",
         		maxMessage: "L'adresse email doit contenir au maximum {{ limit }} caractères."
         	)]
    private ?string $email = null;

	#[ORM\Column(length: 100, nullable: false)]
         	#[Assert\NotBlank(message: "Veuillez renseigner le prénom.")]
         	#[Assert\Length(
         		min: 4,
         		max: 100,
         		minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.",
         		maxMessage: "Le prénom doit contenir au maximum {{ limit }} caractères."
         	)]
	private ?string $firstName = null;

	#[ORM\Column(length: 100, nullable: false)]
         	#[Assert\NotBlank(message: "Veuillez renseigner le nom.")]
         	#[Assert\Length(
         		min: 4,
         		max: 100,
         		minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
         		maxMessage: "Le nom doit contenir au maximum {{ limit }} caractères."
         	)]
	private ?string $name = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

	public function __construct()
	{
         		$this->userRoles = new ArrayCollection();
         		$this->organizations = new ArrayCollection();
         		$this->services = new ArrayCollection();
         		$this->factures = new ArrayCollection();
         		$this->createdOrganizations = new ArrayCollection();
         		$this->invite_users = new ArrayCollection();
	}

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
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

	public function getInviteUsers(): Collection
	{
		return $this->invite_users;
	}

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

	public function getUserRoles(): Collection
	{
		return $this->userRoles;
	}

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

	public function addRole(Role $role)
	{
		if (!$this->userRoles->contains($role)) {
			$this->userRoles->add($role);
			$role->addUser($this);
		}
	}

	public function removeRole(Role $role)
	{
		if ($this->userRoles->contains($role)) {
			$this->userRoles->removeElement($role);
			$role->removeUser($this);
		}
	}

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->password = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

	public function getOrganizations(){
         		return $this->organizations;
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

    public function addOrganization(Organization $organization): static
    {
        if (!$this->organizations->contains($organization)) {
            $this->organizations->add($organization);
            $organization->addUser($this);
        }

        return $this;
    }

    public function removeOrganization(Organization $organization): static
    {
        if ($this->organizations->removeElement($organization)) {
            $organization->removeUser($this);
        }

        return $this;
    }
}