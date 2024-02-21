<?php

namespace App\Security\Voter;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrganizationVoter extends Voter
{
	public const VIEW = "ORGANIZATION_VIEW";
	public const CREATE = "ORGANIZATION_CREATE";
	public const UPDATE = "ORGANIZATION_UPDATE";
	public const INVITE = "ORGANIZATION_INVITE";
	public const REMOVE_USER = "ORGANIZATION_REMOVE_USER";
	public const READ_FACTURE = "ORGANIZATION_READ_FACTURE";
	public const WRITE_FACTURE = "ORGANIZATION_WRITE_FACTURE";
	public const WRITE_DEVIS = "ORGANIZATION_WRITE_DEVIS";
	public const VIEW_STATS = "ORGANIZATION_VIEW_STATS";

	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository
	) {
	}

	protected function supports(string $attribute, mixed $subject): bool
	{
		return (in_array($attribute, [
			self::VIEW,
			self::UPDATE,
			self::INVITE,
			self::REMOVE_USER,
			self::READ_FACTURE,
			self::WRITE_FACTURE,
			self::WRITE_DEVIS,
			self::VIEW_STATS,
		]) &&
			$subject instanceof Organization) ||
			$attribute === self::CREATE;
	}

	protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
	{
		/** @var Organization $organization */
		$organization = $subject;
		/** @var User $user */
		$user = $token->getUser();
		if (!$user instanceof UserInterface) {
			return false;
		}

		if ($attribute === self::CREATE) {
			return $this->organizationRepository->userCanCreateOrganization($user);
		} elseif ($attribute === self::VIEW) {
			return $this->organizationRepository->organizationContainsUser($organization, $user);
		} elseif ($attribute === self::UPDATE) {
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_org");
		} elseif ($attribute === self::INVITE) {
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_user");
		} elseif ($attribute === self::REMOVE_USER) {
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_user");
		} elseif ($attribute === self::WRITE_DEVIS) {
			return $this->roleRepository->userHasPermission($organization, $user, "write_devis");
		} elseif ($attribute === self::READ_FACTURE) {
			return $this->roleRepository->userHasPermission($organization, $user, "read_factures");
		} elseif ($attribute === self::WRITE_FACTURE) {
			return $this->roleRepository->userHasPermission($organization, $user, "write_factures");
		} elseif ($attribute === self::VIEW_STATS) {
			return $this->roleRepository->userHasPermission($organization, $user, "view_stats");
		}
		return false;
	}
}
