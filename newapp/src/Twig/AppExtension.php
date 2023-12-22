<?php
namespace App\Twig;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\RoleRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
	public function __construct(
		private RoleRepository $roleRepository
	)
	{}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('checkPermissionOnOrganization', [$this, 'checkPermissionOnOrganization']),
        ];
    }

    public function checkPermissionOnOrganization(User $user, Organization $organization, string $permission): int
    {
        return $this->roleRepository->checkPermissionOnOrganization($user, $organization, $permission);
    }
}