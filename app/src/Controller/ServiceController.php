<?php

namespace App\Controller;

use App\Controller\MiddlewareController;
use App\Entity\Organization;
use App\Entity\Service;
use App\Form\CreateServiceForm;
use App\Form\UpdateServiceForm;
use App\Middleware\Middleware;
use App\Service\Middleware\UserCanCreateServiceMiddleware;
use App\Service\Middleware\UserCanUpdateServiceMiddleware;
use App\Services\ServiceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends MiddlewareController
{
	#[Route("/organization/{OrganizationId}/services", name: "services", methods: ["GET"])]
	#[Middleware(UserCanCreateServiceMiddleware::class, "validate")]
	public function index()
	{
		/** @var Organization $organization */
		$organization = Middleware::$floor["organization"];

		$services = $organization->getServices();

		return $this->render("service/services.html.twig", [
			"organization" => $organization,
			"services" => $services,
		]);
	}

	#[Route("/organization/{OrganizationId}/service", name: "create_service", methods: ["GET", "POST"])]
	#[Middleware(UserCanCreateServiceMiddleware::class, "validate")]
	public function create(Request $request, ServiceService $serviceService)
	{
		$response = new Response();

		$service = new Service();
		$createServiceForm = $this->createForm(CreateServiceForm::class, $service);
		$createServiceForm->handleRequest($request);

		if ($createServiceForm->isSubmitted() && $createServiceForm->isValid()) {
			$service = $createServiceForm->getData();
			/** @var Organization $organization */
			$organization = Middleware::$floor["organization"];

			try {
				$serviceService->createService($organization, $service);
				$this->addFlash("success", "Le service a bien été créé.");

				// Redirect to services page
				$response->setStatusCode(Response::HTTP_FOUND);
				$response->headers->set("Location", "/organization/" . $organization->getId() . "/services");
				return $response;
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			}
		}

		return $this->render(
			"service/createService.html.twig",
			[
				"createServiceForm" => $createServiceForm->createView(),
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{OrganizationId}/service/{serviceId}",
			name: "update_service",
			methods: ["GET", "POST"]
		)
	]
	#[Middleware(UserCanUpdateServiceMiddleware::class, "validate")]
	public function update(Request $request, ServiceService $serviceService)
	{
		$response = new Response();

		/** @var Service $service */
		$service = Middleware::$floor["service"];
		$updateServiceForm = $this->createForm(UpdateServiceForm::class, $service);
		$updateServiceForm->handleRequest($request);

		if ($updateServiceForm->isSubmitted() && $updateServiceForm->isValid()) {
			$service = $updateServiceForm->getData();
			/** @var Organization $organization */
			$organization = Middleware::$floor["organization"];

			try {
				$serviceService->updateService($organization, $service);
				$this->addFlash("success", "Le service à bien été mis à jour.");

				// Redirect to services page
				$response->setStatusCode(Response::HTTP_FOUND);
				$response->headers->set("Location", "/organization/" . $organization->getId() . "/services");
				return $response;
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			}
		}

		return $this->render(
			"service/updateService.html.twig",
			[
				"updateServiceForm" => $updateServiceForm->createView(),
			],
			$response
		);
	}
}
