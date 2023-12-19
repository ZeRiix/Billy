<?php

namespace App\Middleware\Role;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
// local imports
use App\Middleware\AbstractMiddleware;
use App\Middleware\Middleware;
use App\Middleware\PermissionMiddleware;
use App\Repository\RoleRepository;
use App\Entity\Role;

class UserCanUpdateRoleMiddleware extends AbstractMiddleware
{
	private RoleRepository $roleRepository;
	private Request $request;

	public function __construct(RoleRepository $roleRepository, Request $request)
	{
		$this->roleRepository = $roleRepository;
		$this->request = $request;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		new Middleware(PermissionMiddleware::class, "has", options: "manage_org");

		// get role
		/** @var Role $role */
		$role = $this->roleRepository->findOneBy(["id" => $this->request->attributes->get("roleId")]);

		if ($role->getOrganization() !== Middleware::$floor["organization"]) {
			$this->redirectTo("/organization/" . Middleware::$floor["organization"]->getId() . "/roles");
		}

		return $this->output("exist", $role);
	}
}
