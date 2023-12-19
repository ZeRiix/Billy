<?php

namespace App\Services\Role;

//local import
use App\Repository\RoleRepository;
use App\Entity\Role;
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;

class RoleService
{
	private RoleRepository $roleRepository;
	private UserRepository $userRepository;

	public function __construct(RoleRepository $roleRepository, UserRepository $userRepository)
	{
		$this->roleRepository = $roleRepository;
		$this->userRepository = $userRepository;
	}

	public function getAll(Organization $organization): array
	{
		// get all roles for organization
		$roles = $this->roleRepository->getRolesForOrganization($organization);
		// parse roles to array
		$rolesArray = [];
		foreach ($roles as $role) {
			$rolesArray[] = [
				"id" => $role->getId(),
				"name" => $role->getName(),
				"created_at" => $role->getCreatedAt(),
				"updated_at" => $role->getUpdatedAt(),
			];
		}
		return $rolesArray;
	}

	public function create(Role $role, Organization $organization): Role
	{
		// create role
		$role->setOrganization($organization);
		$this->roleRepository->save($role);

		return $role;
	}

	public function give(User $user, Role $role): void
	{
		// give role to user
		$user->addRole($role);
		$this->userRepository->save($user);
	}

	public function takeOff(User $user, Role $role): void
	{
		// take off role from user
		$user->removeRole($role);
		$this->userRepository->save($user);
	}

	public function delete(Role $role): void
	{
		// delete role
		$this->roleRepository->delete($role);
	}

	public function checkPermission(User $user, Organization $organization, string $permission): bool
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

	public function getOrganizationIsOwner(User $user)
	{
		// get roles for user and organization
		$roles = $this->roleRepository->getRolesForUser($user);
		$organizations = [];
		// check if user has permission
		foreach ($roles as $role) {
			/** @var Role $role **/
			if ($role->getName() === "OWNER") {
				$organizations[] = $role->getOrganization();
			}
		}
		return $organizations;
	}

	public function update(Role $role, Organization $organization)
	{
		if ($role->getOrganization() !== $organization) {
			throw new \Exception("Ce role n'existe pas");
		}
		$this->roleRepository->save($role);
	}
}
