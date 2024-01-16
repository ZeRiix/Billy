<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// local imports
use App\Services\Client\ClientService;
use App\Entity\Client;
use App\Entity\Organization;
use App\Security\Voter\ClientVoter;
// form
use App\Form\CreateClientForm;
use App\Form\UpdateClientForm;

class ClientController extends AbstractController
{
	#[Route("/organization/{organization}/client", name: "app_client", methods: ["GET", "POST"])]
	public function create(Request $request, ClientService $clientService, Organization $organization): Response
	{
		if (!$this->isGranted(ClientVoter::CREATE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour créer un client dans cette organisation.");
			return $this->redirectToRoute("clients");
		}
		$response = new Response();

		$client = new Client();
		$form = $this->createForm(CreateClientForm::class, $client);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Client */
			$client = $form->getData();
			try {
				$clientService->create($organization, $client);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le client a bien été créé.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"client/create_client.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/organization/{organization}/client/{client}", name: "client_delete", methods: ["DELETE"])]
	public function delete(ClientService $clientService, Organization $organization, Client $client)
	{
		if (!$this->isGranted(ClientVoter::UPDATE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour supprimer un client dans cette organisation.");
			return $this->redirectToRoute("clients");
		}
		$clientService->delete($organization, $client);
		$this->redirectToRoute("/organization/" . $organization->getId() . "/clients");
	}

	#[Route("/organization/{organization}/clients", name: "clients", methods: ["GET", "POST"])]
	public function getAll(ClientService $clientService, Organization $organization): Response
	{
		if (!$this->isGranted(ClientVoter::VIEW, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour consulter les clients dans cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		$response = new Response();

		// gets clients
		$clients = $clientService->getAll($organization);

		return $this->render(
			"client/clients.html.twig",
			[
				"clients" => $clients,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/client/{client}",
			name: "app_client_update",
			methods: ["GET", "POST"]
		)
	]
	public function update(Request $request, ClientService $clientService, Client $client, Organization $organization): Response
	{
		if (!$this->isGranted(ClientVoter::UPDATE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour modifier un client dans cette organisation.");
			return $this->redirectToRoute("clients");
		}
		$response = new Response();

		$form = $this->createForm(UpdateClientForm::class, $client);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Client */
			$client = $form->getData();
			try {
				$clientService->update($organization, $client);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le client à bien été modifiée.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"client/update_client.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}
}