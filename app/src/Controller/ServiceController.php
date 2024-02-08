<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Service;
use App\Form\CreateServiceForm;
use App\Security\Voter\ServiceVoter;
use App\Services\ServiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
	#[Route("/organization/{organization}/services", name: "app_services", methods: ["GET"])]
	public function view(Organization $organization): Response
	{
		if (!$this->isGranted(ServiceVoter::VIEW, $organization)) {
			return $this->redirectToRoute("app_organizations");
		}

		$services = $organization->getServices();
		foreach ($services as $service) {
			if ($service->getIsArchived()) {
				$services->removeElement($service);
			}
		}

		return $this->render("service/index.html.twig", [
			"services" => $services,
		]);
	}

	#[
		Route(
			"/organization/{organization}/services/archived",
			name: "app_archived_services",
			methods: ["GET"]
		)
	]
	public function viewArchived(Organization $organization): Response
	{
		if (!$this->isGranted(ServiceVoter::VIEW, $organization)) {
			return $this->redirectToRoute("app_organizations");
		}

		return $this->render("service/index.html.twig", [
			"services" => $organization->getServices(),
			"isArchived" => true,
		]);
	}

	#[Route("/organization/{organization}/service", name: "app_create_service", methods: ["GET", "POST"])]
	public function create(
		Organization $organization,
		Request $request,
		ServiceService $serviceService
	): Response {
		if (!$this->isGranted(ServiceVoter::CREATE, $organization)) {
			return $this->redirectToRoute("app_services", ["id" => $organization->getId()]);
		}

		$service = new Service();
		$createServiceForm = $this->createForm(CreateServiceForm::class, $service);
		$createServiceForm->handleRequest($request);

		if ($createServiceForm->isSubmitted() && $createServiceForm->isValid()) {
			$service = $createServiceForm->getData();

			try {
				$serviceService->createService($organization, $service);
				$this->addFlash("success", "Le service a bien été créé.");
				if ($request->query->get("callback")) {
					return $this->redirect($request->query->get("callback"));
				} else {
					return $this->redirectToRoute("app_services", ["organization" => $organization->getId()]);
				}
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
			}
		}

		return $this->render("service/create.html.twig", [
			"createServiceForm" => $createServiceForm,
			"update" => false,
		]);
	}

	#[
		Route(
			"/organization/{organization}/service/{service}",
			name: "app_update_service",
			methods: ["GET", "POST"]
		)
	]
	public function update(Service $service, Request $request, ServiceService $serviceService): Response
	{
		if (!$this->isGranted(ServiceVoter::UPDATE, $service)) {
			return $this->redirectToRoute("app_services", ["id" => $service->getOrganization()->getId()]);
		}

		$createServiceForm = $this->createForm(CreateServiceForm::class, $service);
		$createServiceForm->handleRequest($request);

		if ($createServiceForm->isSubmitted() && $createServiceForm->isValid()) {
			$service = $createServiceForm->getData();

			try {
				$serviceService->updateService($service->getOrganization(), $service);
				$this->addFlash("success", "Le service a bien été modifié.");
				if ($request->query->get("callback")) {
					return $this->redirect($request->query->get("callback"));
				} else {
					return $this->redirectToRoute("app_services", [
						"organization" => $service->getOrganization()->getId(),
					]);
				}
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
			}
		}

		return $this->render("service/create.html.twig", [
			"createServiceForm" => $createServiceForm,
			"update" => true,
		]);
	}

	#[Route("/service/{service}", name: "app_get_description_service", methods: ["GET"])]
	public function getDescription(Service $service): Response
	{
		return $this->json([
			"description" => $service->getDescription(),
		]);
	}

	#[
		Route(
			"/organization/{organization}/service/{service}/archived",
			name: "app_archive_service",
			methods: ["GET"]
		)
	]
	public function archive(Service $service, ServiceService $serviceService): Response
	{
		if (!$this->isGranted(ServiceVoter::UPDATE, $service)) {
			return $this->redirectToRoute("app_services", ["id" => $service->getOrganization()->getId()]);
		}

		try {
			$serviceService->archiveService($service);
			$this->addFlash("success", "Le service a bien été archivé.");
		} catch (\Exception $error) {
			$this->addFlash("error", $error->getMessage());
		}

		return $this->redirectToRoute("app_services", [
			"organization" => $service->getOrganization()->getId(),
		]);
	}

	#[
		Route(
			"/organization/{organization}/service/{service}/unarchived",
			name: "app_unarchive_service",
			methods: ["GET"]
		)
	]
	public function unarchive(Service $service, ServiceService $serviceService): Response
	{
		if (!$this->isGranted(ServiceVoter::UPDATE, $service)) {
			return $this->redirectToRoute("app_services", ["id" => $service->getOrganization()->getId()]);
		}

		try {
			$serviceService->unArchiveService($service);
			$this->addFlash("success", "Le service a bien été désarchivé.");
		} catch (\Exception $error) {
			$this->addFlash("error", $error->getMessage());
		}

		return $this->redirectToRoute("app_archived_services", [
			"organization" => $service->getOrganization()->getId(),
		]);
	}
}
