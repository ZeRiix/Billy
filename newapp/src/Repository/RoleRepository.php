<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
// local imports
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Organization;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\Traits\SaveTrait;

class RoleRepository extends ServiceEntityRepository
{
	use SaveTrait;

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
		$role->setManageService(true);
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
		$conn = $this->_em->getConnection();
		$sql = 'SELECT * from role as r 
				  INNER JOIN user_role as ur on r.id = ur.role_id AND ur.user_id = :user_id 
				  WHERE organization_id = :organization_id';
		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"user_id" => $user->getId(),
			"organization_id" => $org->getId(),
		]);

		return $res->fetchAllAssociative();
	}

	public function checkPermissionOnOrganization(User $user, Organization $organization, string $permission): bool
	{
		$roles = $this->getUserRolesForOrganization($organization, $user);
		// check if user has permission
		foreach ($roles as $role) {
			return $this->checkPermissionOnRole($role, $permission);
		}
		return false;
	}

	private function checkPermissionOnRole(array $role, string $permission): bool
	{
		if ($role["manage_org"] && $permission === "manage_org") {
			return true;
		}
		if ($role["manage_user"] && $permission === "manage_user") {
			return true;
		}
		if ($role["manage_client"] && $permission === "manage_client") {
			return true;
		}
		if ($role["write_devis"] && $permission === "write_devis") {
			return true;
		}
		if ($role["write_factures"] && $permission === "write_factures") {
			return true;
		}
		if ($role["manage_service"] && $permission === "manage_service") {
			return true;
		}
		return false;
	}

	public function getRolesForUser(User $user): array
	{
		return $this->createQueryBuilder("role")
			->innerJoin("role.users", "user")
			->where("user = :user")
			->setParameter("user", $user)
			->getQuery()
			->getResult();
	}
}