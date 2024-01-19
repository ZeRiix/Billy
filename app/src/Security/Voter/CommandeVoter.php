<?php

namespace App\Security\Voter;

use App\Entity\Commande;
use App\Entity\Devis;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommandeVoter extends Voter
{
	public const CREATE = 'COMMANDE_CREATE';
	public const UPDATE = 'COMMANDE_UPDATE';

	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository,
	)
	{}

	protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::UPDATE]) && (
			$subject instanceof Devis ||
			$subject instanceof Commande
		);
    }

	protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
	{
		$user = $token->getUser();

		if (!$user instanceof UserInterface) {
			return false;
		}

		else if($attribute === self::CREATE) {
			/** @var Devis $subject */
			$organization = $subject->getOrganization();
			if(!$organization) {
				return false;
			}
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "write_devis");
		}
		else if($attribute === self::UPDATE){
			/** @var Commande $subject */
			$organization = $subject->getDevis()->getOrganization();
			if(!$organization) {
				return false;
			}
			return $this->roleRepository->checkPermissionOnOrganization($user, $organization, "write_devis");
		}
		return false;
	}
}