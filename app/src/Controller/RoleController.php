<?php

namespace App\Controller;

use App\Entity\Organization;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// local imports
use App\Services\Role\RoleService;
use App\Security\Voter\RoleVoter;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
// form
use App\Form\SelectRoleForm;
use App\Form\CreateRoleForm;
use App\Form\UpdateRoleForm;


class RoleController extends AbstractController
{
	#[Route("/organization/{organization}/role", name: "app_role", methods: ["GET", "POST"])]
	public function create(Request $request, RoleService $roleService, Organization $organization): Response
	{
		if (!$this->isGranted(RoleVoter::MANAGE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour accéder à réation de role poue cette orgaisation.");
			return $this->redirectToRoute("app_organizations");
		}
		$response = new Response();
		$role = new Role();
		$form = $this->createForm(CreateRoleForm::class, $role);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Role */
			$role = $form->getData();
			try {
				$roleService->create($role, $organization);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le rôle a bien été créé.");
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

	#[Route("/organization/{organization}/user/{user}/selectrole", name: "app_role_select", methods: ["GET", "POST"])]
	public function selectRoleToUser(
		Request $request, 
		RoleService $roleService, 
		Organization $organization, 
		RoleRepository $roleRepository, 
		User $user): Response
	{
		if (!$this->isGranted(RoleVoter::MANAGE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour editer les roles d'un utilisarteur dans cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		$response = new Response();
		$rolesUser = $roleService->getRolesUserHasInOrganization(
			$user,
			$organization
		);
		$form = $this->createForm(SelectRoleForm::class, null, [
			"rolesHas" => $rolesUser,
			"roles" => $roleRepository->getRolesForOrganization($organization),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$selectedRole = $form->getData();
			try {
				$roleService->attribute($user, $selectedRole);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Les droits de l'utilisateur ont été modifié.");
			} catch (\Exception $e) {
				die(var_dump($e->getMessage()));
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"role/select_role.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/organization/{organization}/role/{role}", name: "app_role_delete", methods: ["DELETE"])]
	public function delete(RoleService $roleService, Organization $organization, Role $role)
	{
		if (!$this->isGranted(RoleVoter::MANAGE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour supprimer un role dans cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		$roleService->delete($role);
		$this->redirectToRoute("/organization/" . $organization->getId() . "/roles");
	}

	#[Route("/organization/{organization}/roles", name: "app_role_list", methods: ["GET"])]
	public function list(RoleService $roleService, Organization $organization): Response
	{
		if (!$this->isGranted(RoleVoter::MANAGE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour lister les roles de cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		$response = new Response();
		$roles = $roleService->getAll($organization);
		return $this->render(
			"role/list_role.html.twig",
			[
				"roles" => $roles,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/role/{role}",
			name: "app_role_update",
			methods: ["GET", "POST"]
		)
	]
	public function update(Request $request, RoleService $roleService, Role $role, Organization $organization): Response
	{
		if (!$this->isGranted(RoleVoter::MANAGE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour modifier un role dans cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		$response = new Response();
		if ($role->getName() === "OWNER") {
			$this->redirectToRoute(
				route: "/organization/" . $organization->getId(),
				status: Response::HTTP_UNAUTHORIZED
			);
		}
		$form = $this->createForm(UpdateRoleForm::class, $role);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Role $role */
			$role = $form->getData();
			try {
				$roleService->update($role, $organization);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le rôle à bien été modifiée");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"role/update_role.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}
}