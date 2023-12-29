<?php

namespace App\Controller;

//local
use Symfony\Component\HttpFoundation\Request;
use App\Security\Voter\OrganizationVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
//repository
use App\Repository\OrganizationRepository;
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
    public function index(): Response
    {
		/** @var User $user */
		$user = $this->getUser();

        return $this->render('organization/organizations.html.twig', [
            'organizations' => $user->getOrganizations()
        ]);
    }

	#[Route('/organization/{id}', name: 'app_organization_get_id', methods: ["GET"])]
	#[IsGranted(OrganizationVoter::VIEW, "organization", "message null")]
    public function view(Request $request, Organization $organization): Response
    {
		$imagePath = $_ENV['UPLOAD_IMAGE_PATH'] . $organization->getId() . '.jpeg';
		$imageExist = false;
		if (file_exists($imagePath)) {
			$imageExist = true;
		}
        return $this->render('organization/organization.view.html.twig', [
            'organization' => $organization,
			'imageExist' => $imageExist,
        ]);
    }

	#[Route('/organization', name: 'app_create_organization', methods: ["GET", "POST"])]
	#[IsGranted(OrganizationVoter::CREATE, message: "can't create organization")]
    public function create(Request $request, OrganizationService $organizationService): Response
    {
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
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			} finally {
				return $this->redirectToRoute("app_organization_get_id", [
					"id" => $organization->getId(),
				]);
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
	#[IsGranted(OrganizationVoter::UPDATE, "organization", "message null")]
    public function update(Organization $organization): Response
    {
		$response = new Response();
		$imagePath = $_ENV['UPLOAD_IMAGE_PATH'] . $organization->getId() . '.jpeg';
		$imageExist = file_exists($imagePath);

		$form = $this->createForm(EditOrganizationForm::class, $organization);
		$form->handleRequest($request);

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

        return $this->render('organization/organization.update.html.twig', [
            "form" => $form->createView(),
			"organization" => $organization,
			'imageExist' => $imageExist
        ]);
    }
}
