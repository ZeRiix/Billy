<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
// local imports
use App\Entity\InviteOrganization;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\User;
use App\Repository\Traits\SaveTrait;
use App\Repository\Traits\DeleteTrait;

class InviteOrganizationRepository extends ServiceEntityRepository
{
	use SaveTrait;
	use DeleteTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, InviteOrganization::class);
	}

	public function create(Organization $organization, User $user): InviteOrganization
	{
		$inviteOrganization = new InviteOrganization();
		$inviteOrganization->setOrganization($organization);
		$inviteOrganization->setUser($user);
		$this->getEntityManager()->persist($inviteOrganization);
		$this->getEntityManager()->flush();
		return $inviteOrganization;
	}

	public function getInviteOrganizationByOrganizationAndUser(
		Organization $organization,
		User $user
	): ?InviteOrganization {
		return $this->findOneBy(["organization" => $organization, "user" => $user]);
	}
}