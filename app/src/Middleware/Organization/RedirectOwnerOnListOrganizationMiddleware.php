<?php

namespace App\Middleware\Organization;

use App\Middleware\AbstractMiddleware;
use App\Middleware\Middleware;
use App\Middleware\SelfUserMiddleware;
use App\Repository\OrganizationRepository;

class RedirectOwnerOnListOrganizationMiddleware extends AbstractMiddleware
{
	private OrganizationRepository $organizationRepository;

	public function __construct(OrganizationRepository $organizationRepository)
	{
		$this->organizationRepository = $organizationRepository;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		new Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login");
		$has = $this->organizationRepository->findOneBy(["createdBy" => Middleware::$floor["user"]]);
		if ($has) {
			return $this->redirectTo(
				"/organizations?error=Vous ne pouvez pas créer de nouvelle organisation."
			);
		} else {
			return $this->output("notexist");
		}
	}
}
