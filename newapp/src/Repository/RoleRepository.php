<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
// local imports
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\Organization;
use App\Entity\Role;
use App\Entity\User;

class RoleRepository extends ServiceEntityRepository
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
		$role->setManageService(true);
		$role->setOrganization($organization);
		$role->addUser($user);
		$this->save($role);
		return $role;
	}

	/**
	 * @param T $entity
	 */
	public function save($role)
	{
		$this->_em->persist($role);
		$this->_em->flush();

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

	public function checkPermissionOnOrganization(User $user, Organization $organization, string $permission): bool
	{
		// get roles for user and organization
		/** @var Role $role */
		$roles = $this->roleRepository->getUserRolesForOrganization($organization, $user);
		// check if user has permission
		foreach ($roles as $role) {
			if ($role->getManageOrg() && $permission === "manage_org") {
				return true;
			}
			if ($role->getManageUser() && $permission === "manage_user") {
				return true;
			}
			if ($role->getManageClient() && $permission === "manage_client") {
				return true;
			}
			if ($role->getWriteDevis() && $permission === "write_devis") {
				return true;
			}
			if ($role->getWriteFactures() && $permission === "write_factures") {
				return true;
			}
			if ($role->getManageService() && $permission === "manage_service") {
				return true;
			}
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
