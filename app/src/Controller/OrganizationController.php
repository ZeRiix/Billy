<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Entity\Organization;
use App\Form\CreateOrganizationFormType;
use App\Services\Organization\OrganizationService;
use App\Middleware\Middleware;
use App\Middleware\SelfUserMiddleware;

class OrganizationController extends MiddlewareController
{
	#[Route("/organization", name: "app_organization", methods: ["GET", "POST"])]
	#[Middleware(SelfUserMiddleware::class, "has", output: "user", redirectTo: "/login")]
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
}
