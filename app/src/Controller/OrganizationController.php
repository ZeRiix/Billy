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
use App\Entity\User;
use App\Services\Role\RoleService;
use App\Services\Organization\OrganizationService;
use App\Middleware\Organization\UserCanLeaveOrganizationMiddleware;
use App\Middleware\OrganizationContainsUserMiddleware;
use App\Middleware\Organization\RedirectOwnerOnListOrganizationMiddleware;
// form
use App\Form\CreateOrganizationForm;
use App\Form\DeleteOrganizationForm;
use App\Form\InviteUserForm;
use App\Middleware\GetOrganizationMiddleware;
use App\Form\LeaveOrganizationByForm;
use App\Form\LeaveOrganizationForm;
use App\Form\EditOrganizationForm;

class OrganizationController extends MiddlewareController
{
	#[Route("/organization", name: "app_organization", methods: ["GET", "POST"])]
	#[Middleware(RedirectOwnerOnListOrganizationMiddleware::class, "notexist")]
	public function create(Request $request, OrganizationService $organizationService): Response
	{
		$response = new Response();
		$organization = new Organization();
		$form = $this->createForm(CreateOrganizationForm::class, $organization);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Organization */
			$organization = $form->getData();
			try {
				$organizationService->create($organization, Middleware::$floor["user"]);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'organisation a bien été créée.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			} finally {
				return $this->redirectToRoute("app_organization_get_id", [
					"OrganizationId" => $organization->getId(),
				]);
			}
		}

		return $this->render(
			"organization/create.html.twig",
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
				$this->addFlash("success", "L'organisation a bien été supprimée.");
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
	#[Middleware(PermissionMiddleware::class, "has", options: "manage_user")]
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
				$this->addFlash(
					"success",
					"L'utilisateur a bien été invité dans l'organisation : " .
						Middleware::$floor["organization"]->getName() .
						"."
				);
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
			"/organization/{OrganizationId}/user/{UserId}/join",
			name: "organization_join_user",
			methods: ["GET"]
		)
	]
	#[Middleware(GetOrganizationMiddleware::class, "exist", output: "organization", redirectTo: "/dashboard")]
	public function join(Request $request, OrganizationService $organizationService): Response
	{
		$response = new Response();
		try {
			$organizationService->join(Middleware::$floor["organization"], $request->get("UserId"));
			$response->setStatusCode(Response::HTTP_OK);
			$this->addFlash("success", "L'utilisateur a bien rejoint l'organisation.");
		} catch (\Exception $e) {
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->addFlash("error", $e->getMessage());
		}
		return $this->redirectToRoute("app_organization_get_id", [
			"OrganizationId" => Middleware::$floor["organization"]->getId(),
		]);
	}

	#[
		Route(
			"/organization/{OrganizationId}/leave",
			name: "organization_leave_user",
			methods: ["GET", "POST"]
		)
	]
	#[Middleware(UserCanLeaveOrganizationMiddleware::class)]
	public function leave(Request $request, OrganizationService $organizationService): Response
	{
		$response = new Response();
		$form = $this->createForm(LeaveOrganizationForm::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$organizationService->leave(Middleware::$floor["user"], Middleware::$floor["organization"]);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Vous avez bien quitté l'organisation.");
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
			"/organization/{OrganizationId}/user/{UserId}/leave",
			name: "organization_leave_user_by",
			methods: ["GET", "POST"]
		)
	]
	#[Middleware(PermissionMiddleware::class, "has", options: "manage_user")]
	public function leave_user_by(Request $request, OrganizationService $organizationService): Response
	{
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
				$this->addFlash("success", "L'utilisateur a bien quitté l'organisation.");
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

	#[Route("/organizations", name: "app_organizations_by_user", methods: ["GET"])]
	#[Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login")]
	public function organizations(OrganizationService $organizationService): Response
	{
		$canCreate = false;
		$org = $organizationService->getCreatedBy(Middleware::$floor["user"]);
		if ($org) {
			$canCreate = true;
		}
		$response = new Response();
		$organizations = [];
		//Faire un getOrganizations depuis le User en question
		/** @var User $user */
		$user = Middleware::$floor["user"];
		/** @var Organization[] $organizations */
		$organizations = $user->getOrganizations();
		//die(var_dump($organizations));
		$response->setStatusCode(Response::HTTP_OK);
		return $this->render(
			"organization/organizations.html.twig",
			[
				"organizations" => $organizations,
				"canCreate" => $canCreate,
			],
			$response
		);
	}

	#[Route("/organization/{OrganizationId}/edit", name: "app_organization_edit", methods: ["POST", "GET"])]
	#[Middleware(PermissionMiddleware::class, "has", options: "manage_org")]
	public function edit(Request $request, OrganizationService $organizationService): Response
	{
		/** @var \Entity\Organization $organization */
		$organization = Middleware::$floor["organization"];

		$response = new Response();
		$form = $this->createForm(EditOrganizationForm::class, $organization);
		$form->handleRequest($request);
		$organizationHaveImage = file_exists($_ENV["UPLOAD_IMAGE_PATH"] . $organization->getId() . ".jpeg");

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Organization */
			$organization = $form->getData();
			try {
				$organizationService->modify($organization);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'organisation à bien été modifiée.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/edit.html.twig",
			[
				"form" => $form->createView(),
				"organization" => $organization,
				"organizationHaveImage" => $organizationHaveImage,
			],
			$response
		);
	}

	#[Route("/organization/{OrganizationId}", name: "app_organization_get_id", methods: ["GET"])]
	#[Middleware(OrganizationContainsUserMiddleware::class, "has")]
	public function getOrganizationById(): Response
	{
		$response = new Response();
		$organization = Middleware::$floor["organization"];
		$organizationHaveImage = file_exists($_ENV["UPLOAD_IMAGE_PATH"] . $organization->getId() . ".jpeg");

		return $this->render(
			"organization/index.html.twig",
			[
				"organization" => $organization,
				"organizationHaveImage" => $organizationHaveImage,
			],
			$response
		);
	}
}
