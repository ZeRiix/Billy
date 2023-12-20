<?php

namespace App\Middleware\Organization;

use App\Middleware\AbstractMiddleware;
use App\Middleware\Middleware;
use App\Middleware\SelfUserMiddleware;
use App\Middleware\GetOrganizationMiddleware;

class UserCanLeaveOrganizationMiddleware extends AbstractMiddleware
{
	public function handler(mixed $input, ?array $options): mixed
	{
		new Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login");
		new Middleware(
			GetOrganizationMiddleware::class,
			"exist",
			output: "organization",
			redirectTo: "/dashboard"
		);
		return null;
	}
}
