<?php

namespace App\Service\Middleware;

use App\Entity\User;
use App\Entity\Organization;
use App\Middleware\AbstractMiddleware;
use App\Middleware\AccessTokenMiddleware;
use App\Middleware\Middleware;
use App\Repository\OrganizationRepository;
use App\Services\Role\RoleService;
use App\Services\Token\AccessTokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserCanCreateServiceMiddleware extends AbstractMiddleware
{
	private OrganizationRepository $organizationRepository;
	private Request $request;
	private RoleService $roleService;

	public function __construct(
		OrganizationRepository $organizationRepository,
		Request $request,
		RoleService $roleService
	) {
		$this->organizationRepository = $organizationRepository;
		$this->request = $request;
		$this->roleService = $roleService;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		$nameAccessToken = AccessTokenService::getName();
		$pathAccessToken = AccessTokenService::$path;

		new Middleware(
			AccessTokenMiddleware::class,
			"exist",
			output: "user",
			httpException: new HttpException(
				Response::HTTP_FOUND,
				headers: [
					"Location" => "/login",
					"Set-Cookie" => "$nameAccessToken=deleted; path=$pathAccessToken; expires=Thu, 01 Jan 1970 00:00:00 GMT",
				]
			)
		);
		$orgId = $this->request->attributes->get("organizationId");
		/** @var Organization $organization */
		$organization = $this->organizationRepository->getById($orgId);
		if (!$organization) {
			$this->redirectTo("/dashboard");
		}
		/** @var User */
		$user = Middleware::$floor["user"];

		if ($organization->getUsers()->contains($user)) {
			return $this->output("userNotBelongs");
		}

		Middleware::$floor["organization"] = $organization;

		$has = $this->roleService->checkPermission($user, $organization, "manage_service");

		if (!$has) {
			$this->redirectTo("/organization/$orgId");
		}

		return $this->output("validate");
	}
}
