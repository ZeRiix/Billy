<?php
namespace App\Twig;

use App\Entity\Organization;
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
			new TwigFunction('getCurrentOrganization', [$this, 'getCurrentOrganization']),
        ];
    }

    public function checkPermissionOnOrganization(User $user, string $permission): bool
    {
		$organization = $this->getCurrentOrganization();
		if(!$organization){
			return false;
		}

        return $this->roleRepository->checkPermissionOnOrganization($user, $organization, $permission);
    }

	public function getCurrentOrganization(): ?Organization
	{
		$request = $this->requestStack->getMainRequest();
		$organizationId = $request->get("organization");
		if(!$organizationId){
			return null;
		}

		return $this->organizationRepository->find($organizationId);
	}
}