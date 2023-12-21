<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
// local imports
use App\Entity\InviteOrganization;
use App\Entity\Organization;
use App\Repository\BaseRepository;
use App\Entity\User;

class InviteOrganizationRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, InviteOrganization::class);
	}

	public function create(Organization $organization, User $user): InviteOrganization
	{
		$inviteOrganization = new InviteOrganization();
		$inviteOrganization->setOrganization($organization);
		$inviteOrganization->setUser($user);
		$this->save($inviteOrganization);
		return $inviteOrganization;
	}

	public function getInviteOrganizationByOrganizationAndUser(
		Organization $organization,
		User $user
	): ?InviteOrganization {
		return $this->findOneBy(["organization" => $organization, "user" => $user]);
	}
}
