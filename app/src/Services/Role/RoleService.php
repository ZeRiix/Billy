<?php

namespace App\Services\Role;

//local import
use App\Repository\RoleRepository;
use App\Entity\Role;
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Validator\Constraints\Regex;

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
		$roleFound = $this->roleRepository->findOneBy([
			"organization" => $organization, 
			"name" => $role
		]);
		if($roleFound){
			throw new Exception("Un role portant ce nom existe déjà.");
		}

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
		return $this->roleRepository->userHasPermission($organization, $user, $permission);
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
		$roleFound = $this->roleRepository->findOneBy([
			"organization" => $organization, 
			"name" => $role
		]);
		if($roleFound && $roleFound->getId() !== $role->getId()){
			throw new Exception("Un role portant ce nom existe déjà.");
		}

		if ($role->getOrganization() !== $organization) {
			throw new \Exception("Ce role n'existe pas");
		}
		$this->roleRepository->save($role);
	}

	public function getRolesUserDoesNotHaveInOrganization(User $user, Organization $organization)
	{
		// affreux
		$rolesUserInOrganization = $this->roleRepository->getUserRolesForOrganization($organization, $user);
		$roles = $this->roleRepository->getRolesForOrganization($organization);
		foreach ($rolesUserInOrganization as $roleUserInOrganization) {
			foreach ($roles as $key => $role) {
				if ($roleUserInOrganization["id"] == $role->getId() || $role->getName() === "OWNER") {
					unset($roles[$key]);
				}
			}
		}

		return $roles;
	}

	public function getRolesUserHasInOrganization(User $user, Organization $organization)
	{
		return $this->roleRepository->getUserRolesForOrganization($organization, $user);
		
	}

	public function attribute(User $user, array $select)
	{
		foreach ($select as $key => $value) {
			if ($value) {
				$role = $this->roleRepository->find($key);
				if ($role) {
					$this->give($user, $role);
				}
			} else {
				$role = $this->roleRepository->find($key);
				if ($role) {
					$this->takeOff($user, $role);
				}
			}
		}
	}
}