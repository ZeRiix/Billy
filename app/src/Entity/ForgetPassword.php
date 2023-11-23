<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

// local imports
use App\Repository\UserRegisterRepository;

#[ORM\Entity(repositoryClass: UserRegisterRepository::class)]
#[ORM\Table(name: "user_register")]
#[ORM\HasLifecycleCallbacks]
class ForgetPassword
{
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: "CUSTOM")]
	#[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
	private ?Uuid $id;

	#[ORM\OneToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: false)]
	private User $user;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	//methods

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getUser(): User
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
	public function setCreatedAt(): self
	{
		$this->created_at = new \DateTimeImmutable();

		return $this;
	}
}
