<?php

namespace App\Service\Middleware;

use App\Entity\User;
use App\Entity\Service;
use App\Middleware\AbstractMiddleware;
use App\Middleware\Middleware;
use App\Middleware\SelfUserMiddleware;
use App\Repository\ServiceRepository;
use App\Services\Role\RoleService;
use App\Services\Token\AccessTokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserCanUpdateServiceMiddleware extends AbstractMiddleware
{
	private ServiceRepository $serviceRepository;
	private Request $request;
	private RoleService $roleService;

	public function __construct(
		ServiceRepository $serviceRepository,
		Request $request,
		RoleService $roleService
	) {
		$this->serviceRepository = $serviceRepository;
		$this->request = $request;
		$this->roleService = $roleService;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		$nameAccessToken = AccessTokenService::getName();
		$pathAccessToken = AccessTokenService::$path;

		new Middleware(
			SelfUserMiddleware::class,
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

		$serviceId = $this->request->attributes->get("serviceId");
		$organizationId = $this->request->attributes->get("organizationId");
		/** @var Service $service */
		$service = $this->serviceRepository->getById($serviceId);
		if (!$service) {
			$this->redirectTo("/dashboard");
		}
		/** @var User */
		$user = Middleware::$floor["user"];
		$organization = $service->getOrganization();

		if ($organization->getId() != $organizationId) {
			$this->redirectTo("/organization/$organizationId");
		}

		Middleware::$floor["service"] = $service;
		Middleware::$floor["organization"] = $organization;

		$has = $this->roleService->checkPermission($user, $organization, "manage_service");

		if (!$has) {
			$this->redirectTo("/organization/$organizationId");
		}

		return $this->output("validate");
	}
}
