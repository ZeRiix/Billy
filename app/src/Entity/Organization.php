<?php

namespace App\Entity;

use App\Repository\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Organization
{
	#[ORM\Id]
	#[ORM\Column(type: "uuid", unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $address = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank(message: "Veuillez renseigner l\'email de l\'organisation.")]
	#[Assert\Email(message: "Veuillez renseigner un email valide.")]
	#[
		Assert\Length(
			min: 5,
			max: 255,
			minMessage: "L\'email doit contenir au moins {{ limit }} caractères.",
			maxMessage: "L\'email ne peut pas dépasser {{ limit }} caractères."
		)
	]
	private ?string $email = null; //Pas testé si regex fonctionne à test quand on aura un form

	#[ORM\Column(length: 10)]
	#[Assert\NotBlank(message: "Veuillez renseigner le numéro de téléphone de l\'organisation.")]
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

	#[ORM\Column(length: 100)]
	#[Assert\NotBlank(message: "Veuillez renseigner l'activité de votre organisation.")]
	#[
		Assert\Length(
			min: 2,
			max: 50,
			minMessage: "L'activité doit contenir au moins {{ limit }} caractères.",
			maxMessage: "L'activité ne peut pas dépasser {{ limit }} caractères."
		)
	]
	private ?string $activity = null;

	#[ORM\Column(length: 14, nullable: false)]
	#[Assert\NotBlank(message: "Veuillez renseigner le siret de l'organisation.")]
	#[Assert\Length(min: 14, max: 14, exactMessage: "Le siret doit contenir {{ limit }} caractères.")]
	private ?string $siret = null;

	#[ORM\OneToMany(mappedBy: "organization", targetEntity: Role::class)]
	private Collection $roles;

	#[ORM\OneToMany(mappedBy: "organization", targetEntity: Service::class)]
	private Collection $services;

	#[ORM\OneToMany(mappedBy: "organization", targetEntity: Devis::class)]
	private Collection $devis;

	#[ORM\OneToMany(mappedBy: "organization", targetEntity: Facture::class)]
	private Collection $factures;

	#[ORM\OneToMany(mappedBy: "organization", targetEntity: Client::class)]
	private Collection $clients;

	#[ORM\OneToMany(mappedBy: "organization", targetEntity: InviteOrganization::class)]
	private Collection $invite_organizations;

	#[ORM\ManyToMany(targetEntity: User::class, inversedBy: "organizations")]
	#[ORM\JoinColumn(nullable: true)]
	#[ORM\JoinTable(name: "user_organizations")]
	private Collection $users;

	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: "createdOrganizations")]
	#[ORM\JoinColumn(nullable: false)]
	private User $createdBy;

	#[Vich\UploadableField(mapping: "organizationImage", fileNameProperty: "logoName")]
	#[
		Assert\Image(
			mimeTypes: ["image/jpeg"],
			mimeTypesMessage: "Le format de l'image doit être au format jpeg.",
			maxSize: "2M",
			maxSizeMessage: "L'image ne doit pas dépasser 2Mo."
		)
	]
	#[Ignore]
	private ?File $logoFile = null;

	#[ORM\Column(nullable: true)]
	private ?string $logoName = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $create_at = null;
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

	public function setId(Uuid $id): static
	{
		$this->id = $id;

		return $this;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getAddress(): ?string
	{
		return $this->address;
	}

	public function setAddress(string $address): static
	{
		$this->address = $address;

		return $this;
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

	public function setActivity(string $activity): static
	{
		$this->activity = $activity;

		return $this;
	}

	public function getSiret(): ?string
	{
		return $this->siret;
	}

	public function setSiret(string $siret): static
	{
		$this->siret = $siret;

		return $this;
	}

	/**
	 * @return Collection<int, Role>
	 */
	public function getRoles(): Collection
	{
		return $this->roles;
	}

	public function addRole(Role $role): static
	{
		if (!$this->roles->contains($role)) {
			$this->roles->add($role);
			$role->setOrganization($this);
		}

		return $this;
	}

	public function removeRole(Role $role): static
	{
		if ($this->roles->removeElement($role)) {
			// set the owning side to null (unless already changed)
			if ($role->getOrganization() === $this) {
				$role->setOrganization(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Service>
	 */
	public function getServices(): Collection
	{
		return $this->services;
	}

	public function addService(Service $service): static
	{
		if (!$this->services->contains($service)) {
			$this->services->add($service);
			$service->setOrganization($this);
		}

		return $this;
	}

	public function removeService(Service $service): static
	{
		//na aucun sens mais flemme de re passer derrier
		if ($this->services->removeElement($service)) {
			// set the owning side to null (unless already changed)
			if ($service->getOrganization() === $this) {
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Devis>
	 */
	public function getDevis(): Collection
	{
		return $this->devis;
	}

	public function addDevis(Devis $devis): static
	{
		if (!$this->devis->contains($devis)) {
			$this->devis->add($devis);
			$devis->setOrganization($this);
		}

		return $this;
	}

	public function removeDevis(Devis $devis): static
	{
		if ($this->devis->removeElement($devis)) {
			// set the owning side to null (unless already changed)
			if ($devis->getOrganization() === $this) {
				$devis->setOrganization(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Facture>
	 */
	public function getFactures(): Collection
	{
		return $this->factures;
	}

	public function addFacture(Facture $facture): static
	{
		if (!$this->factures->contains($facture)) {
			$this->factures->add($facture);
			$facture->setOrganization($this);
		}

		return $this;
	}

	public function removeFacture(Facture $facture): static
	{
		// une fois pas deux...
		if ($this->factures->removeElement($facture)) {
			// set the owning side to null (unless already changed)
			if ($facture->getOrganization() === $this) {
				$facture->setOrganization(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Client>
	 */
	public function getClients(): Collection
	{
		return $this->clients;
	}

	public function addClient(Client $client): static
	{
		if (!$this->clients->contains($client)) {
			$this->clients->add($client);
			$client->setOrganization($this);
		}

		return $this;
	}

	public function removeClient(Client $client): static
	{
		if ($this->clients->removeElement($client)) {
			// set the owning side to null (unless already changed)
			if ($client->getOrganization() === $this) {
				$client->setOrganization(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, InviteOrganization>
	 */
	public function getInviteOrganizations(): Collection
	{
		return $this->invite_organizations;
	}

	public function addInviteOrganization(InviteOrganization $inviteOrganization): static
	{
		if (!$this->invite_organizations->contains($inviteOrganization)) {
			$this->invite_organizations->add($inviteOrganization);
			$inviteOrganization->setOrganization($this);
		}

		return $this;
	}

	public function removeInviteOrganization(InviteOrganization $inviteOrganization): static
	{
		//jamais 2 sans 3
		if ($this->invite_organizations->removeElement($inviteOrganization)) {
			// set the owning side to null (unless already changed)
			if ($inviteOrganization->getOrganization() === $this) {
				$inviteOrganization->setOrganization(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, User>
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function addUser(User $user): static
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
		}

		return $this;
	}

	public function removeUser(User $user): static
	{
		$this->users->removeElement($user);

		return $this;
	}

	public function getCreatedBy(): User
	{
		return $this->createdBy;
	}

	public function setCreatedBy(User $createdBy): static
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	public function setLogoFile(?File $logoFile = null): void
	{
		$this->logoFile = $logoFile;

		if ($logoFile) {
			$this->setUpdatedAtValue();
		}
	}

	public function getLogoFile(): ?File
	{
		return $this->logoFile;
	}

	public function setLogoName(?string $logoName): void
	{
		$this->logoName = $logoName;
	}

	public function getLogoName(): ?string
	{
		return $this->logoName;
	}

	public function getCreateAt(): ?\DateTimeImmutable
	{
		return $this->create_at;
	}

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updated_at;
	}

	#[ORM\PrePersist]
	public function setCreatedAtValue(): void
	{
		$this->create_at = new \DateTimeImmutable();
	}

	// je vais rien dire sur le nomage mais on ce comprend
	#[ORM\PrePersist]
	#[ORM\PreUpdate]
	public function setUpdatedAtValue(): void
	{
		$this->updated_at = new \DateTimeImmutable();
	}

	public function __serialize(): array
	{
		return [
			"id" => $this->id,
			"name" => $this->name,
			"address" => $this->address,
			"email" => $this->email,
			"phone" => $this->phone,
			"activity" => $this->activity,
			"siret" => $this->siret,
			"logoName" => $this->logoName,
			"create_at" => $this->create_at,
			"updated_at" => $this->updated_at,
		];
	}

	public function __unserialize(array $data): void
	{
		$this->id = $data["id"];
		$this->name = $data["name"];
		$this->address = $data["address"];
		$this->email = $data["email"];
		$this->phone = $data["phone"];
		$this->activity = $data["activity"];
		$this->siret = $data["siret"];
		$this->logoName = $data["logoName"];
		$this->create_at = $data["create_at"];
		$this->updated_at = $data["updated_at"];
	}
}
