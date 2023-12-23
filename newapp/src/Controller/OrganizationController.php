<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Security\Voter\OrganizationVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

	#[Route('/organization/{id}', name: 'app_organization', methods: ["GET"])]
	#[IsGranted(OrganizationVoter::VIEW, "organization", "message null")]
    public function view(Organization $organization): Response
    {
        return $this->render('organization/organization.view.html.twig', [
            'controller_name' => 'OrganizationController',
        ]);
    }

	#[Route('/organization', name: 'app_create_organization', methods: ["GET", "POST"])]
	#[IsGranted(OrganizationVoter::CREATE, message: "message null")]
    public function create(): Response
    {
        return $this->render('organization/organization.create.html.twig', [
            'controller_name' => 'OrganizationController',
        ]);
    }

	#[Route('/organization/{id}/edit', name: 'app_update_organization', methods: ["GET", "POST"])]
	#[IsGranted(OrganizationVoter::UPDATE, "organization", "message null")]
    public function update(Organization $organization): Response
    {
        return $this->render('organization/organization.update.html.twig', [
            'controller_name' => 'OrganizationController',
        ]);
    }
}
