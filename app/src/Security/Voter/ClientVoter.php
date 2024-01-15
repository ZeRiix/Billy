<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    public const VIEW = 'CLIENT_VIEW';
    public const CREATE = 'CLIENT_CREATE';
    public const UPDATE = 'CLIENT_UPDATE';

	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository
	)
	{}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
			in_array($attribute, [self::CREATE, self::UPDATE, self::VIEW]) && (
				$subject instanceof Organization ||
				$subject instanceof Client
			)
		);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

		if($attribute === self::VIEW) {
			/** @var Organization $subject */
			return $this->organizationRepository->organizationContainsUser($subject, $user);
		}
		else if($attribute === self::CREATE) {
			/** @var Organization $subject */
			return $this->roleRepository->checkPermissionOnOrganization($user, $subject, "manage_client");
		}
		else if($attribute === self::UPDATE){
			/** @var Client $subject */
			$organization = $subject->getOrganization();
			if(!$organization) return false;
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "manage_client");
		}
		else return false;
    }
}
