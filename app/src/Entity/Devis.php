<?php

namespace App\Entity;

//séparer les "import" serre uniquement a faire semble de montré que tu as de la rigeur
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

// local imports
use App\Repository\DevisRepository;
use App\Entity\Organization;
use App\Entity\Facture;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Ignore;

enum DeviStatus: string
{
	case EDITING = "editing";
	case LOCK = "lock";
	case SIGN = "sign";
	case COMPLETED = "completed";
	case CANCELED = "canceled";
}

#[ORM\Entity(repositoryClass: DevisRepository::class)]
#[ORM\Table(name: "`devis`")]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Devis
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: "devis")]
	private ?Organization $organization = null;

	#[ORM\OneToMany(targetEntity: Facture::class, mappedBy: "devis")]
	private Collection $factures;

	#[ORM\ManyToOne(targetEntity: Client::class, inversedBy: "devis")]
	#[ORM\JoinColumn(nullable: true)]
	private ?Client $client = null;

	#[ORM\OneToMany(mappedBy: "devis", targetEntity: Commande::class)]
	private Collection $commandes;

	#[ORM\Column(length: 100, nullable: false)]
	#[Assert\NotBlank(message: "Veuillez renseigner le nom du devis.")]
	#[
		Assert\Length(
			min: 3,
			max: 100,
			minMessage: "Le nom du devis doit contenir au moins {{ limit }} caractères.",
			maxMessage: "Le nom du devis doit contenir au maximum {{ limit }} caractères."
		)
	]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $description = null;

	#[ORM\Column(type: Types::STRING, enumType: DeviStatus::class)]
	private DeviStatus $status = DeviStatus::EDITING;

	#[ORM\Column(type: Types::DECIMAL, nullable: true, precision: 10, scale: 2)]
	private ?string $total_ht = null;

	#[ORM\Column(type: Types::DECIMAL, nullable: true, precision: 10, scale: 2)]
	private ?string $total_ttc = null;

	#[ORM\Column(type: Types::INTEGER, nullable: true, precision: 10, scale: 2)]
	#[
		Assert\Range(
			min: 0,
			max: 100,
			notInRangeMessage: "Le taux de remise doit être compris entre 0 et 100."
		)
	]
	private ?int $discount = 0;

	#[Vich\UploadableField(mapping: "imageSign", fileNameProperty: "imageSignName")]
	#[
		Assert\Image(
			mimeTypes: ["image/jpeg"],
			mimeTypesMessage: "Le format de l'image doit être au format jpeg.",
			maxSize: "2M",
			maxSizeMessage: "L'image ne doit pas dépasser 2Mo."
		)
	]
	#[Ignore]
	private ?File $imageSign = null;

	#[ORM\Column(nullable: true)]
	private ?string $imageSignName = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

	public function __construct()
	{
		$this->factures = new ArrayCollection();
		$this->commandes = new ArrayCollection();
	}

	public function getId(): ?int
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

	public function getFactures(): Collection
	{
		return $this->factures;
	}

	public function getClient(): ?Client
	{
		return $this->client;
	}

	public function setClient(?Client $client): self
	{
		$this->client = $client;

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

	public function getStatus(): DeviStatus
	{
		return $this->status;
	}

	public function setStatus(DeviStatus $status): static
	{
		$this->status = $status;

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

	public function getDiscount(): ?int
	{
		return $this->discount;
	}

	public function setDiscount(?int $discount): static
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

	/**
	 * @return Collection<int, Commande>
	 */
	public function getCommandes(): Collection
	{
		return $this->commandes;
	}

	public function addCommande(Commande $commande): static
	{
		if (!$this->commandes->contains($commande)) {
			$this->commandes->add($commande);
			$commande->setDevis($this);
		}

		return $this;
	}

	public function removeCommande(Commande $commande): static
	{
		if ($this->commandes->removeElement($commande)) {
			if ($commande->getDevis() === $this) {
				$commande->setDevis(null);
			}
		}

		return $this;
	}

	public function setImageSign(?File $imageSign = null): static
	{
		$this->imageSign = $imageSign;

		if ($imageSign) {
			$this->setUpdatedAt();
		}

		return $this;
	}

	public function getImageSign(): ?File
	{
		return $this->imageSign;
	}

	public function getImageSignName(): ?string
	{
		return $this->imageSignName;
	}

	public function setImageSignName(string $imageSignName): static
	{
		$this->imageSignName = $imageSignName;

		return $this;
	}
}
