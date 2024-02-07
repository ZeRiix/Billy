<?php

namespace App\Controller;

//local
use Symfony\Component\HttpFoundation\Request;
use App\Security\Voter\OrganizationVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//services
use App\Services\Organization\OrganizationService;
//entity
use App\Entity\Organization;
use App\Entity\User;
//form
use App\Form\CreateOrganizationForm;
use App\Form\EditOrganizationForm;
use App\Form\InviteUserForm;
use App\Form\LeaveOrganizationForm;
use App\Form\LeaveOrganizationByForm;

class OrganizationController extends AbstractController
{
	#[Route("/organizations", name: "app_organizations", methods: ["GET"])]
	public function index(OrganizationService $organizationService): Response
	{
		/** @var User $user */
		$user = $this->getUser();
		$canCreate = true;
		$org = $organizationService->getCreatedBy($user);
		if ($org) {
			$canCreate = false;
		}

		return $this->render("organization/organizations.html.twig", [
			"organizations" => $user->getOrganizations(),
			"canCreate" => $canCreate,
		]);
	}

	#[Route("/organization/{organization}", name: "app_organization_get_id", methods: ["GET"])]
	public function view(Organization $organization): Response
	{
		//vérifier si le user est bien "vérifié" en db sinon l'empêcher de se connecter et lui demander de contacter l'admin
		if (!$this->isGranted(OrganizationVoter::VIEW, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour accéder à cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		return $this->render("organization/organization.view.html.twig", [
			"organization" => $organization,
		]);
	}

	#[Route("/organization", name: "app_create_organization", methods: ["GET", "POST"])]
	public function create(Request $request, OrganizationService $organizationService): Response
	{
		if (!$this->isGranted(OrganizationVoter::CREATE)) {
			$this->addFlash("error", "Vous possédez déjà une organisation.");
			return $this->redirectToRoute("app_organizations");
		}

		$response = new Response();
		/** @var Organization $organization */
		$organization = new Organization();
		$form = $this->createForm(CreateOrganizationForm::class, $organization);
		$form->handleRequest($request);
		/** @var User $user */
		$user = $this->getUser();

		if ($form->isSubmitted() && $form->isValid() && $user !== null) {
			/** @var Organization */
			$organization = $form->getData();
			try {
				$organization = $organizationService->create($organization, $user);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'organisation a bien été créée.");
				return $this->redirectToRoute("app_organization_get_id", [
					"organization" => $organization->getId(),
				]);
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/organization.create.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/organization/{organization}/edit", name: "app_update_organization", methods: ["GET", "POST"])]
	public function update(
		Request $request,
		Organization $organization,
		OrganizationService $organizationService
	): Response {
		if (!$this->isGranted(OrganizationVoter::UPDATE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour éditer cette organisation.");
			return $this->redirectToRoute("app_organization_get_id", [
				"organization" => $organization->getId(),
			]);
		}
		$response = new Response();
		$form = $this->createForm(EditOrganizationForm::class, $organization);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Organization */
			$organization = $form->getData();
			try {
				$organizationService->modify($organization);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'organisation {$organization->getName()} à bien été modifiée.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/organization.update.html.twig",
			[
				"form" => $form->createView(),
				"organization" => $organization,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/invite",
			name: "organization_invite_user",
			methods: ["GET", "POST"]
		)
	]
	public function invite(
		Request $request,
		OrganizationService $organizationService,
		Organization $organization
	): Response {
		if (!$this->isGranted(OrganizationVoter::INVITE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour inviter un utilsateur");
			return $this->redirectToRoute("app_organization_get_id", [
				"organization" => $organization->getId(),
			]);
		}
		$response = new Response();
		$form = $this->createForm(InviteUserForm::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			try {
				$organizationService->invite($data["email"], $organization);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash(
					"success",
					"L'utilisateur a bien été invité dans l'organisation : " . $organization->getName() . "."
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
			"/organization/{organization}/user/{user}/join",
			name: "organization_join_user",
			methods: ["GET"]
		)
	]
	public function join(
		Request $request,
		OrganizationService $organizationService,
		Organization $organization,
		User $user
	): Response {
		$response = new Response();
		try {
			$organizationService->join($organization, $user);
			$response->setStatusCode(Response::HTTP_OK);
			$this->addFlash("success", "L'utilisateur a bien rejoint l'organisation.");
		} catch (\Exception $e) {
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->addFlash("error", $e->getMessage());
		}
		return $this->render("organization/organization.view.html.twig", [
			"organization" => $organization,
		]);
	}

	#[Route("/organization/{id}/leave", name: "organization_leave_user", methods: ["GET", "POST"])]
	public function leave(
		Request $request,
		OrganizationService $organizationService,
		Organization $organization
	): Response {
		/** @var User $user */
		$user = $this->getUser();

		$response = new Response();
		$form = $this->createForm(LeaveOrganizationForm::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$organizationService->leave($user, $organization);
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
			"/organization/{organization}/user/{user}/leave",
			name: "organization_leave_user_by",
			methods: ["GET", "POST"]
		)
	]
	public function leave_user_by(
		Request $request,
		OrganizationService $organizationService,
		Organization $organization,
		User $user
	): Response {
		if (!$this->isGranted(OrganizationVoter::REMOVE_USER, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour supprimer un utilisateur");
			return $this->redirectToRoute("app_organization_get_id", [
				"organization" => $organization->getId(),
			]);
		}

		$response = new Response();
		try {
			$organizationService->leave($user, $organization);
			$response->setStatusCode(Response::HTTP_OK);
			$this->addFlash("success", "L'utilisateur a bien quitté l'organisation.");
		} catch (\Exception $e) {
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->addFlash("error", $e->getMessage());
		}

		return $this->redirectToRoute("organization_list_users", [
			"organization" => $organization->getId(),
		]);
	}

	#[Route("/organization/{organization}/users", name: "organization_list_users", methods: ["GET"])]
	public function list_users(
		Request $request,
		OrganizationService $organizationService,
		Organization $organization
	): Response {
		if (!$this->isGranted(OrganizationVoter::VIEW, $organization)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour lister les utilisateurs de cette organisation."
			);
			return $this->redirectToRoute("app_organizations");
		}
		$response = new Response();
		$users = $organizationService->getUsers($organization);
		return $this->render(
			"organization/list_users.html.twig",
			[
				"users" => $users,
			],
			$response
		);
	}
}
