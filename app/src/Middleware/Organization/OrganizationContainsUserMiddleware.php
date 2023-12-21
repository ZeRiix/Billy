<?php

namespace App\Middleware;

use App\Repository\OrganizationRepository;

class OrganizationContainsUserMiddleware extends AbstractMiddleware
{
	private OrganizationRepository $organizationRepository;

	public function __construct(OrganizationRepository $organizationRepository)
	{
		$this->organizationRepository = $organizationRepository;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		new Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login");
		new Middleware(
			GetOrganizationMiddleware::class,
			"exist",
			output: "organization",
			redirectTo: "/dashboard"
		);
		$has = $this->organizationRepository->organizationContainsUser(
			Middleware::$floor["organization"],
			Middleware::$floor["user"]
		);
		if ($has) {
			return $this->output("has");
		} else {
			return $this->redirectTo("/organizations?error=Vous n'êtes pas membre de cette organisation.");
		}
	}
}
