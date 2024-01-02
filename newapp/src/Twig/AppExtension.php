<?php
namespace App\Twig;

use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository,
		private RequestStack $requestStack
	)
	{}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('checkPermissionOnOrganization', [$this, 'checkPermissionOnOrganization']),
        ];
    }

    public function checkPermissionOnOrganization(User $user, string $permission): int
    {
		$request = $this->requestStack->getMainRequest();
		$organizationId = $request->get("organizationId");
		if(!$organizationId){
			return false;
		}

		$organization = $this->organizationRepository->find($organizationId);

        return $this->roleRepository->checkPermissionOnOrganization($user, $organization, $permission);
    }
}