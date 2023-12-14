<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
// local imports
use App\Repository\BaseRepository;
use App\Entity\Organization;
use App\Entity\Role;
use App\Entity\User;

class RoleRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Role::class);
	}

	public function setOwner(User $user, Organization $organization): Role
	{
		$role = new Role();
		$role->setName("OWNER");
		$role->setManageOrg(true);
		$role->setManageUser(true);
		$role->setManageClient(true);
		$role->setWriteDevis(true);
		$role->setWriteFactures(true);
		$role->setOrganization($organization);
		$role->addUser($user);
		$this->save($role);

		return $role;
	}

	public function getRolesForOrganization(Organization $org): array
	{
		return $this->findBy(["organization" => $org]);
	}

	public function getUserRolesForOrganization(Organization $org, User $user): array
	{
		return $this->createQueryBuilder("role")
			->innerJoin("role.organization", "org")
			->innerJoin("role.users", "user")
			->where("org = :organization")
			->andWhere("user = :user")
			->setParameter("organization", $org)
			->setParameter("user", $user)
			->getQuery()
			->getResult();
	}
}
