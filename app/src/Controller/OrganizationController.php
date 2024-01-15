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

class OrganizationController extends AbstractController
{
    #[Route('/organizations', name: 'app_organizations', methods: ["GET"])]
    public function index(OrganizationService $organizationService): Response
    {
		/** @var User $user */
		$user = $this->getUser();
		$canCreate = true;
		$org = $organizationService->getCreatedBy($user);
		if ($org) {
			$canCreate = false;
		}

        return $this->render('organization/organizations.html.twig', [
            'organizations' => $user->getOrganizations(),
			'canCreate' => $canCreate
        ]);
    }

	#[Route('/organization/{organization}', name: 'app_organization_get_id', methods: ["GET"])]
    public function view(Organization $organization): Response
    {
		//vérifier si le user est bien "vérifié" en db sinon l'empêcher de se connecter et lui demander de contacter l'admin
		if (!$this->isGranted(OrganizationVoter::VIEW, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour accéder à cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
        return $this->render('organization/organization.view.html.twig', [
            'organization' => $organization
        ]);
    }

	#[Route('/organization', name: 'app_create_organization', methods: ["GET", "POST"])]
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

	#[Route('/organization/{id}/edit', name: 'app_update_organization', methods: ["GET", "POST"])]
    public function update(Request $request, Organization $organization, OrganizationService $organizationService): Response
    {
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
		
        return $this->render('organization/organization.update.html.twig', [
			"form" => $form->createView(),
			"organization" => $organization
		], $response
		);
    }
}