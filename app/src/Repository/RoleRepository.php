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
		$role->setReadDevis(true);
		$role->setReadFactures(true);
		$role->setViewStats(true);
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

	public function userHasPermission(Organization $org, User $user, string $permission): bool
	{
		if (preg_match("/[a-z_]+/", $permission) === false) {
			return false;
		}

		$conn = $this->_em->getConnection();
		$sql =
			'WITH org_role as (
			SELECT * FROM role WHERE organization_id = :organization_id
		), user_org_role as (
			SELECT org_role.* FROM org_role 
			INNER JOIN user_role on org_role.id = user_role.role_id
			WHERE user_role.user_id = :user_id
		)
		SELECT * from user_org_role WHERE ' .
			$permission .
			" = TRUE";

		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"user_id" => $user->getId(),
			"organization_id" => $org->getId(),
			"permission" => $permission,
		]);

		return isset($res->fetchAllAssociative()[0]);
	}

	public function isOwner(User $user): bool
	{
		$conn = $this->_em->getConnection();
		$sql = "SELECT * from role
		        INNER JOIN user_role on user_id = :user_id AND role_id = id
		        WHERE name = 'OWNER'";
		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"user_id" => $user->getId(),
		]);

		return count($res->fetchAllAssociative()) > 0;
	}

	public function checkPermissionOnOrganization(
		User $user,
		Organization $organization,
		string $permission
	): bool {
		return $this->userHasPermission($organization, $user, $permission);
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
