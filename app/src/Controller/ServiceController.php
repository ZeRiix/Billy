<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Service;
use App\Security\Voter\ServiceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return $this->render('service/create.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    #[Route('/organization/{organization}/service', name: 'app_create_service', methods: ["GET", "POST"])]
    public function create(Organization $organization): Response
    {
		if(!$this->isGranted(ServiceVoter::CREATE, $organization)){
			return $this->redirectToRoute("app_services", ["id" => $organization->getId()]);
		}

        return $this->render('service/create.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

	#[Route('/organization/{organization}/service/{id}', name: 'app_update_service', methods: ["GET", "POST"])]
    public function update(Service $service): Response
    {
		if(!$this->isGranted(ServiceVoter::UPDATE, $service)){
			return $this->redirectToRoute("app_services", ["id" => $service->getOrganization()->getId()]);
		}

        return $this->render('service/update.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
}
