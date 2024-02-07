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
    public const VIEW = 'ORGANIZATION_VIEW';
    public const CREATE = 'ORGANIZATION_CREATE';
    public const UPDATE = 'ORGANIZATION_UPDATE';
	public const INVITE = 'ORGANIZATION_INVITE';
	public const REMOVE_USER = 'ORGANIZATION_REMOVE_USER';
	public const WRITE_DEVIS = 'ORGANIZATION_WRITE_DEVIS';

	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository
	)
	{}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
			in_array($attribute, [self::VIEW, self::UPDATE, self::INVITE, self::REMOVE_USER, self::WRITE_DEVIS]) && 
			$subject instanceof Organization
		) || $attribute === self::CREATE;
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

		if($attribute === self::CREATE) {
			return $this->organizationRepository->userCanCreateOrganization($user);
		}
		else if($attribute === self::VIEW){
			return $this->organizationRepository->organizationContainsUser($organization, $user);
		}
		else if($attribute === self::UPDATE){
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_org");
		}
		else if($attribute === self::INVITE){
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_user");
		}
		else if($attribute === self::REMOVE_USER){
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_user");
		}
		else if($attribute === self::REMOVE_USER){
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_user");
		}
		else if($attribute === self::WRITE_DEVIS){
			return $this->roleRepository->userHasPermission($organization, $user, "write_devis");
		}
		
		return false;
    }
}