<?php

namespace App\Security\Voter;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommandeVoter extends Voter
{
	public const VIEW = 'COMMANDE_VIEW';
	public const CREATE = 'COMMANDE_CREATE';
	public const UPDATE = 'COMMANDE_UPDATE';

	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository,
	)
	{}

	protected function supports(string $attribute, mixed $subject): bool
    {
        return (
			in_array($attribute, [self::VIEW, self::UPDATE]) && 
			$subject instanceof Organization
		) || $attribute === self::CREATE;
    }

	protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
	{
		$user = $token->getUser();

		if (!$user instanceof UserInterface) {
			return false;
		}

		if($attribute === self::VIEW) {
			/** @var Organization $subject */
			//return $this->organizationRepository->organizationContainsUser($subject, $user);
		}
		else if($attribute === self::CREATE) {
			/** @var Organization $subject */
			return $this->organizationRepository->organizationContainsUser($subject, $user)
				&& $this->roleRepository->checkPermissionOnOrganization($user, $subject, "write_devis");
		}
		else if($attribute === self::UPDATE){
			/** @var Organization $subject */
			return $this->roleRepository->checkPermissionOnOrganization($user, $subject, "write_devis");
		}
		return false;
	}
}