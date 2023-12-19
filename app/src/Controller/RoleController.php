<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Middleware\Middleware;
use App\Middleware\SelfUserMiddleware;
use App\Middleware\PermissionMiddleware;
use App\Services\Role\RoleService;
use App\Entity\Role;
// form
use App\Form\GiveRoleForm;
use App\Form\GiveRoleData;
use App\Form\DeleteRoleForm;
use App\Form\CreateRoleForm;

class RoleController extends MiddlewareController
{
	#[Route("/role/{OrganizationId}/create", name: "app_role", methods: ["GET", "POST"])]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_org"],
			redirectTo: "/dashboard"
		)
	]
	public function create(Request $request, RoleService $roleService): Response
	{
		$response = new Response();
		$role = new Role();
		$form = $this->createForm(CreateRoleForm::class, $role);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Role */
			$role = $form->getData();
			try {
				$roleService->create($role, Middleware::$floor["organization"]);
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
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_user"],
			redirectTo: "/dashboard"
		)
	]
	public function giveRoleToUser(Request $request, RoleService $roleService): Response
	{
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

	#[Route("/role/{OrganizationId}/delete", name: "app_role_delete", methods: ["GET", "POST"])]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_user"],
			redirectTo: "/dashboard"
		)
	]
	public function delete(Request $request, RoleService $roleService): Response
	{
		$response = new Response();
		$form = $this->createForm(DeleteRoleForm::class, null, [
			"organization_id" => $request->get("OrganizationId"),
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			try {
				$roleService->delete($data["role"]);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le rôle à bien été supprimé.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}
		return $this->render(
			"role/delete_role.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/roles/{OrganizationId}", name: "app_role_list", methods: ["GET"])]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_user"],
			redirectTo: "/dashboard"
		)
	]
	public function list(RoleService $roleService): Response
	{
		$response = new Response();
		$roles = $roleService->getAll(Middleware::$floor["organization"]);
		return $this->render(
			"role/list_role.html.twig",
			[
				"roles" => $roles,
			],
			$response
		);
	}
}
