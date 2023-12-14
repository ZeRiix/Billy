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
	private OrganizationRepository $organizationRepository;

	public function __construct(
		RoleRepository $roleRepository,
		UserRepository $userRepository,
		OrganizationRepository $organizationRepository
	) {
		$this->roleRepository = $roleRepository;
		$this->userRepository = $userRepository;
		$this->organizationRepository = $organizationRepository;
	}

	public function getAll(Organization $organization): array
	{
		// get all roles for organization
		$roles = $this->roleRepository->getAllForOrganization($organization);
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

	public function create(Role $role, string $organization): Role
	{
		//get Organization
		$org = $this->organizationRepository->findOneById($organization);
		// create role
		$role->setOrganization($org);
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

	public function checkPermission(User $user, string $organization, string $permission): bool
	{
		// get organization
		$org = $this->organizationRepository->findOneById($organization);
		// get roles for user and organization
		$roles = $this->roleRepository->getUserRolesForOrganization($org, $user);
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
		}
		return false;
	}
}
