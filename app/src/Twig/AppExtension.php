<?php
namespace App\Twig;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
	private Serializer $serializer;
	private ?Organization $organization;

	public function __construct(
		private RoleRepository $roleRepository,
		private OrganizationRepository $organizationRepository,
		private RequestStack $requestStack
	) {
		$classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
		$this->serializer = new Serializer([
			new UidNormalizer(),
			new ObjectNormalizer($classMetadataFactory),
		]);
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction("checkPermissionOnOrganization", [$this, "checkPermissionOnOrganization"]),
			new TwigFunction("getCurrentOrganization", [$this, "getCurrentOrganization"]),
			new TwigFunction("entityToJson", [$this, "entityToJson"]),
			// new TwigFunction("absolute_url", [$this, "absoluteUrl"]),
		];
	}

	public function checkPermissionOnOrganization(User $user, string $permission): bool
	{
		$organization = $this->getCurrentOrganization();
		if (!$organization) {
			return false;
		}

		return $this->roleRepository->userHasPermission($organization, $user, $permission);
	}

	public function getCurrentOrganization(): ?Organization
	{
		if (!empty($this->organization)) {
			return $this->organization;
		}

		$request = $this->requestStack->getMainRequest();
		$organizationId = $request->get("organization");
		if (!$organizationId) {
			return $this->organization = null;
		}

		return $this->organization = $this->organizationRepository->find($organizationId);
	}

	public function entityToJson(mixed $entity, array $groups)
	{
		return json_encode($this->serializer->normalize($entity, null, ["groups" => $groups]));
	}

	public function absoluteUrl(string $path)
	{
		return $_ENV["APP_URL"] . $path;
	}
}
