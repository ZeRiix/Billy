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
	#[Route('/organization/{organization}/services', name: 'app_services', methods: ["GET", "POST"])]
    public function view(Organization $organization): Response
    {
		if(!$this->isGranted(ServiceVoter::VIEW, $organization)){
			return $this->redirectToRoute("app_organizations");
		}

        return $this->render('service/index.html.twig', [
            "services" => $organization->getServices()
        ]);
    }

    #[Route('/organization/{organization}/service', name: 'app_create_service', methods: ["GET", "POST"])]
    public function create(Organization $organization, Request $request, ServiceService $serviceService): Response
    {
		if(!$this->isGranted(ServiceVoter::CREATE, $organization)){
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
				if($request->query->get("callback")){
					return $this->redirect($request->query->get("callback"));
				}
				else {
					return $this->redirectToRoute("app_services", ["organization" => $organization->getId()]);
				}
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
			}
		}


        return $this->render('service/create.html.twig', [
			"createServiceForm" => $createServiceForm,
			"update" => false,
        ]);
    }

	#[Route('/organization/{organization}/service/{id}', name: 'app_update_service', methods: ["GET", "POST"])]
    public function update(Service $service, Request $request, ServiceService $serviceService): Response
    {
		if(!$this->isGranted(ServiceVoter::UPDATE, $service)){
			return $this->redirectToRoute("app_services", ["id" => $service->getOrganization()->getId()]);
		}

		$createServiceForm = $this->createForm(CreateServiceForm::class, $service);
		$createServiceForm->handleRequest($request);

		if ($createServiceForm->isSubmitted() && $createServiceForm->isValid()) {
			$service = $createServiceForm->getData();

			try {
				$serviceService->updateService($service->getOrganization(), $service);
				$this->addFlash("success", "Le service a bien été modifié.");
				if($request->query->get("callback")){
					return $this->redirect($request->query->get("callback"));
				}
				else {
					return $this->redirectToRoute("app_services", ["organization" => $service->getOrganization()->getId()]);
				}
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
			}
		}

        return $this->render('service/create.html.twig', [
			"createServiceForm" => $createServiceForm,
			"update" => true
        ]);
    }

	#[Route('/service/{service}', name: "app_get_description_service", methods: ["GET"])]
	public function getDescription(Service $service): Response
	{
		return $this->json([
			"description" => $service->getDescription()
		]);
	}
}
