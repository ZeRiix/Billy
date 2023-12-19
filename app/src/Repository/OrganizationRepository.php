<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class OrganizationRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Organization::class);
	}

	public function findOneByName($value): ?Organization
	{
		return $this->findOneBy(["name" => $value]);
	}

	public function findUserInOrganization(User $user): ?Organization
	{
		return $this->findOneBy(["users" => $user]);
	}

	public function findOneBySiret($value): ?Organization
	{
		return $this->findOneBy(["siret" => $value]);
	}
}
