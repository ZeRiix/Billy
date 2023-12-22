<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\Service;
use App\Security\Voter\ServiceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ServiceController extends AbstractController
{
	#[Route('/organization/{id}/services', name: 'app_services', methods: ["GET", "POST"])]
	#[IsGranted(ServiceVoter::VIEW, "organization", "message null")]
    public function view(Organization $organization): Response
    {
        return $this->render('service/create.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    #[Route('/organization/{id}/service', name: 'app_create_service', methods: ["GET", "POST"])]
	#[IsGranted(ServiceVoter::CREATE, "organization", "message null")]
    public function create(Organization $organization): Response
    {
        return $this->render('service/create.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

	#[Route('/organization/{slug}/service/{id}', name: 'app_update_service', methods: ["GET", "POST"])]
	#[IsGranted(ServiceVoter::UPDATE, "service", "message null")]
    public function update(Service $service): Response
    {
        return $this->render('service/update.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
}
