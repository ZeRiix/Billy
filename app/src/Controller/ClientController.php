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

class ClientController extends AbstractController
{
	#[Route("/organization/{organization}/clients", name: "app_clients", methods: ["GET"])]
	public function getAll(Request $request, Organization $organization): Response
	{
		if (!$this->isGranted(ClientVoter::VIEW, $organization)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour consulter les clients dans cette organisation."
			);
			return $this->redirectToRoute("app_organizations");
		}
		// gets clients
		if ($request->query->get("archived")) {
			$clients = $organization->getClients();
		} else {
			$clients = $organization->getClients()->filter(fn(Client $client) => !$client->getIsArchived());
		}

		return $this->render(
			"client/clients.html.twig",
			[
				"clients" => $clients,
				"isArchived" => $request->query->get("archived") ? true : false,
			],
			new Response(status: Response::HTTP_OK)
		);
	}

	#[Route("/organization/{organization}/clients/archived", name: "app_archived_clients", methods: ["GET"])]
	public function geAllArchivedClients(Organization $organization): Response
	{
		if (!$this->isGranted(ClientVoter::VIEW, $organization)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour consulter les clients dans cette organisation."
			);
			return $this->redirectToRoute("app_organizations");
		}
		$response = new Response();

		return $this->render(
			"client/clients.html.twig",
			[
				"clients" => $organization->getClients(),
				"isArchived" => true,
			],
			$response
		);
	}

	#[Route("/organization/{organization}/client", name: "app_client_create", methods: ["GET", "POST"])]
	public function create(
		Request $request,
		ClientService $clientService,
		Organization $organization
	): Response {
		if (!$this->isGranted(ClientVoter::CREATE, $organization)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour créer un client dans cette organisation."
			);
			return $this->redirectToRoute("app_clients", ["organization" => $organization->getId()]);
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
				return $this->redirectToRoute("app_clients", ["organization" => $organization->getId()]);
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"client/create_client.html.twig",
			[
				"form" => $form->createView(),
				"isCreating" => true,
				"organization" => $organization,
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
	public function update(
		Request $request,
		ClientService $clientService,
		Client $client,
		Organization $organization
	): Response {
		if (!$this->isGranted(ClientVoter::UPDATE, $client)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour modifier un client dans cette organisation."
			);
			return $this->redirectToRoute("app_clients", ["organization" => $organization->getId()]);
		}
		$response = new Response();

		$form = $this->createForm(CreateClientForm::class, $client);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Client */
			$client = $form->getData();
			try {
				$clientService->update($organization, $client);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le client à bien été modifié.");
				return $this->redirectToRoute("app_clients", ["organization" => $organization->getId()]);
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"client/create_client.html.twig",
			[
				"form" => $form->createView(),
				"isEntrprise" => !!$client->getSiret(),
				"organization" => $organization,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/client/{client}/archived",
			name: "app_client_archive",
			methods: ["GET"]
		)
	]
	public function archive(
		Client $client,
		ClientService $clientService,
		Organization $organization
	): Response {
		if (!$this->isGranted(ClientVoter::UPDATE, $client)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour archiver un client dans cette organisation."
			);
			return $this->redirectToRoute("app_clients", ["organization" => $organization->getId()]);
		}
		$response = new Response();
		try {
			$clientService->archiveClient($client);
			$response->setStatusCode(Response::HTTP_OK);
			if ($client->getIsArchived()) {
				$this->addFlash("success", "Le client a bien été archivé.");
			} else {
				$this->addFlash("success", "Le client a bien été désarchivé.");
			}
		} catch (\Exception $e) {
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->addFlash("error", $e->getMessage());
		}

		if ($client->getIsArchived()) {
			return $this->redirectToRoute("app_clients", ["organization" => $organization->getId()]);
		}

		return $this->redirectToRoute("app_clients", [
			"organization" => $organization->getId(),
			"archived" => true,
		]);
	}
}
