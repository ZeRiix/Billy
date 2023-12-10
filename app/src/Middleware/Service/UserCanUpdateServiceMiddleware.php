<?php

namespace App\Service\Middleware;

use App\Entity\User;
use App\Entity\Service;
use App\Middleware\AbstractMiddleware;
use App\Middleware\AccessTokenMiddleware;
use App\Middleware\Middleware;
use App\Repository\ServiceRepository;
use App\Services\Token\AccessTokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserCanUpdateServiceMiddleware extends AbstractMiddleware
{
	private ServiceRepository $serviceRepository;
	private Request $request;

	public function __construct(ServiceRepository $serviceRepository, Request $request)
	{
		$this->serviceRepository = $serviceRepository;
		$this->request = $request;
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

		$serviceId = $this->request->attributes->get("service_id");
		$organizationId = $this->request->attributes->get("organization_id");
		/** @var Service $service */
		$service = $this->serviceRepository->getById($serviceId);
		if (!$service) {
			$this->redirectTo("/dashboard");
		}
		/** @var User */
		$user = Middleware::$floor["user"];
		$organization = $service->getOrganization();

		if ($organization->getId() !== $organizationId) {
			return $this->output("idIsNotSame");
		}

		if ($organization->getUsers()->contains($user)) {
			return $this->output("userNotBelongs");
		}

		Middleware::$floor["service"] = $service;
		Middleware::$floor["organization"] = $organization;

		//checkeck permission

		return $this->output("validate");
	}
}
