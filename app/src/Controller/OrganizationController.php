<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Form\CreateOrganizationFormType;
use App\Services\Organization\OrganizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends AbstractController
{
	private OrganizationService $organizationService;

	public function __construct(OrganizationService $organizationService)
	{
		$this->organizationService = $organizationService;
	}

	#[
		Route(
			"/organization",
			name: "app_organization",
			methods: ["GET", "POST"]
		)
	]
	public function create(Request $request): Response
	{
		$organization = new Organization();
		$form = $this->createForm(
			CreateOrganizationFormType::class,
			$organization
		);
		$response = new Response();
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Organization */
			$organization = $form->getData();
			try {
				$this->organizationService->createOrganization($organization);
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
}
