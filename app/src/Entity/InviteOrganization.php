<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use App\Repository\InviteOrganizationRepository;

#[ORM\Entity(repositoryClass: InviteOrganizationRepository::class)]
#[ORM\Table(name: "`invite_organization`")]
#[ORM\HasLifecycleCallbacks]
class InviteOrganization
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[
		ORM\ManyToOne(
			targetEntity: Organization::class,
			inversedBy: "invite_organizations",
			cascade: ["persist"]
		)
	]
	#[ORM\JoinColumn(nullable: false)]
	private Organization $organization;

	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: "invite_users", cascade: ["persist"])]
	#[ORM\JoinColumn(nullable: false)]
	private User $user;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $updated_at = null;

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

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(User $user): self
	{
		$this->user = $user;

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
