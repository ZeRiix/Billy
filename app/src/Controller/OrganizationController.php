<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Middleware\Middleware;
use App\Middleware\PermissionMiddleware;
use App\Middleware\SelfUserMiddleware;
use App\Entity\Organization;
use App\Services\Role\RoleService;
use App\Services\Organization\OrganizationService;
// form
use App\Form\CreateOrganizationFormType;
use App\Form\DeleteOrganizationForm;
use App\Form\InviteUserForm;
use App\Middleware\GetOrganizationMiddleware;
use App\Form\LeaveOrganizationByForm;
use App\Form\LeaveOrganizationForm;

class OrganizationController extends MiddlewareController
{
	#[Route("/organization", name: "app_organization", methods: ["GET", "POST"])]
	#[Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login")]
	public function create(Request $request, OrganizationService $organizationService): Response
	{
		$response = new Response();
		$organization = new Organization();
		$form = $this->createForm(CreateOrganizationFormType::class, $organization);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Organization */
			$organization = $form->getData();
			try {
				$organizationService->create($organization, Middleware::$floor["user"]);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'organisation à bien été créée.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/index.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/organization/delete", name: "organization_delete", methods: ["GET", "POST"])]
	#[Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login")]
	public function delete(
		Request $request,
		OrganizationService $organizationService,
		RoleService $roleService
	): Response {
		$response = new Response();
		$form = $this->createForm(DeleteOrganizationForm::class, null, [
			"organizations" => $roleService->getOrganizationIsOwner(Middleware::$floor["user"]),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			try {
				$organizationService->delete($data["organization"]);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'organisation à bien été supprimée.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/delete_organization.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{OrganizationId}/invite",
			name: "organization_invite_user",
			methods: ["GET", "POST"]
		)
	]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_user"],
			redirectTo: "/dashboard"
		)
	]
	public function invite(Request $request, OrganizationService $organizationService): Response
	{
		$response = new Response();
		$form = $this->createForm(InviteUserForm::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			try {
				$organizationService->invite($data["email"], Middleware::$floor["organization"]);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'utilisateur à bien été invité.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}
		return $this->render(
			"organization/invite_user.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{OrganizationId}/{UserId}/join",
			name: "organization_join_user",
			methods: ["GET"]
		)
	]
	#[
		Middleware(
			GetOrganizationMiddleware::class,
			"exist",
			output: "organization",
			redirectTo: "/dashboard"
		)
	]
	public function join(Request $request, OrganizationService $organizationService): Response
	{
		$response = new Response();
		try {
			$organizationService->join($request->get("UserId"), Middleware::$floor["organization"]);
			$response->setStatusCode(Response::HTTP_OK);
			$this->addFlash("success", "L'utilisateur à bien rejoint l'organisation.");
		} catch (\Exception $e) {
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->addFlash("error", $e->getMessage());
		}

		return $this->redirectToRoute("app_organization");
	}

	#[
		Route(
			"/organization/{OrganizationId}/leave",
			name: "organization_leave_user",
			methods: ["GET, POST"]
		)
	]
	#[
		Middleware(
			GetOrganizationMiddleware::class,
			"exist",
			output: "organization",
			redirectTo: "/dashboard"
		)
	]
	#[Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login")]
	public function leave(Request $request, OrganizationService $organizationService): Response
	{
		$response = new Response();
		$form = $this->createForm(LeaveOrganizationForm::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$organizationService->leave(
					Middleware::$floor["user"],
					Middleware::$floor["organization"]
				);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'utilisateur à bien quitté l'organisation.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/leave_user.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{OrganizationId}/leave/{UserId}",
			name: "organization_leave_user_by",
			methods: ["GET, POST"]
		)
	]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_user"],
			redirectTo: "/dashboard"
		)
	]
	public function leave_user_by(
		Request $request,
		OrganizationService $organizationService
	): Response {
		$response = new Response();
		$form = $this->createForm(LeaveOrganizationByForm::class, null, [
			"users" => Middleware::$floor["organization"]->getUsers(),
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			try {
				$organizationService->leave($data["user"], Middleware::$floor["organization"]);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'utilisateur à bien quitté l'organisation.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/leave_user_by.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}
}
