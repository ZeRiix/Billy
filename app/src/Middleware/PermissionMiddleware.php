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

	public function handler(mixed $input, ?array $options): mixed
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
			$options["permission"]
		);
		if ($role) {
			return $this->output("exist", $role);
		} else {
			return $this->output("You dont have permission");
		}
	}
}
