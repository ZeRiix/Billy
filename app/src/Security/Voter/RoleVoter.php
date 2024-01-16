<?php

namespace App\Security\Voter;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RoleVoter extends Voter
{
	public const MANAGE = 'MANAGE_ROLE';

	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository
	)
	{}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
			in_array($attribute, [self::MANAGE]) && 
			$subject instanceof Organization
		) || $attribute === self::MANAGE;
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

		if($attribute === self::MANAGE) {
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_user");
		}
		
		return false;
    }
}