<?php

namespace App\Middleware;

use App\Services\Role\RoleService;

class PermissionMiddleware extends AbstractMiddleware
{
	private RoleService $roleService;

	public function __construct(RoleService $roleService)
	{
		$this->roleService = $roleService;
	}

	public function handler(mixed $input, mixed $options): mixed
	{
		new Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login");

		new Middleware(
			GetOrganizationMiddleware::class,
			"exist",
			output: "organization",
			redirectTo: "/dashboard"
		);

		$role = $this->roleService->checkPermission(
			Middleware::$floor["user"],
			Middleware::$floor["organization"],
			$options
		);

		$orgId = Middleware::$floor["organization"]->getId();

		if ($role) {
			return $this->output("has");
		} else {
			$this->redirectTo("/organization/$orgId");
		}
	}
}
