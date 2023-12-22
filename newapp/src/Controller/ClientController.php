<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Organization;
use App\Security\Voter\ClientVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ClientController extends AbstractController
{
	#[Route('/organization/{id}/clients', name: 'app_clients', methods: ["GET"])]
	#[IsGranted(ClientVoter::VIEW, "organization", "message null")]
    public function view(Organization $organization): Response
    {
        return $this->render('client/create.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    #[Route('/organization/{id}/client', name: 'app_create_client', methods: ["GET", "POST"])]
	#[IsGranted(ClientVoter::CREATE, "organization", "message null")]
    public function create(Organization $organization): Response
    {
        return $this->render('client/create.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

	#[Route('/organization/{slug}/client/{id}', name: 'app_update_client', methods: ["GET", "POST"])]
	#[IsGranted(ClientVoter::UPDATE, "client", "message null")]
    public function update(Client $client): Response
    {
        return $this->render('client/update.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
}
