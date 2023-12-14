<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Middleware\Middleware;
use App\Middleware\SelfUserMiddleware;
use App\Services\Role\RoleService;
use App\Form\CreateRoleForm;
use App\Entity\Role;
use App\Form\GiveRoleForm;
use App\Form\GiveRoleData;

class RoleController extends MiddlewareController
{
	#[Route("/role/{OrganizationId}", name: "app_role", methods: ["GET", "POST"])]
	#[Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login")]
	public function create(Request $request, RoleService $roleService): Response
	{
		// check permissions
		$role = $roleService->checkPermission(
			Middleware::$floor["user"],
			$request->get("OrganizationId"),
			"create_role"
		);
		$response = new Response();
		$role = new Role();
		$form = $this->createForm(CreateRoleForm::class, $role);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Role */
			$role = $form->getData();
			try {
				$roleService->create($role, $request->get("OrganizationId"));
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le rôle à bien été créé.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"role/index.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/role/{OrganizationId}/give", name: "app_role_give", methods: ["GET", "POST"])]
	#[Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login")]
	public function giveRoleToUser(Request $request, RoleService $roleService): Response
	{
		// check permissions
		$role = $roleService->checkPermission(
			Middleware::$floor["user"],
			$request->get("OrganizationId"),
			"create_role"
		);
		$response = new Response();
		$giveRoleData = new GiveRoleData();
		$form = $this->createForm(GiveRoleForm::class, $giveRoleData, [
			"organization_id" => $request->get("OrganizationId"),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			try {
				$roleService->give($data->getUser(), $data->getRole());
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le rôle à bien été donné à l'utilisateur.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"role/give_role.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}
}
