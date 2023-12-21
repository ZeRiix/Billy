<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Middleware\Middleware;
use App\Middleware\PermissionMiddleware;
use App\Service\Middleware\UserCanUpdateClientMiddleware;
use App\Services\Client\ClientService;
use App\Entity\Client;
// form
use App\Form\CreateClientForm;
use App\Form\UpdateClientForm;

class ClientController extends MiddlewareController
{
	#[Route("/organization/{OrganizationId}/client", name: "app_client", methods: ["GET", "POST"])]
	#[Middleware(PermissionMiddleware::class, "has", options: "manage_client")]
	public function create(Request $request, ClientService $clientService): Response
	{
		$response = new Response();

		$client = new Client();
		$form = $this->createForm(CreateClientForm::class, $client);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Client */
			$client = $form->getData();
			try {
				$clientService->create(Middleware::$floor["organization"], $client);
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

	#[Route("/organization/{OrganizationId}/client/{ClientId}", name: "client_delete", methods: ["DELETE"])]
	#[Middleware(UserCanUpdateClientMiddleware::class, "exist", output: "client")]
	public function delete(ClientService $clientService): void
	{
		$clientService->delete(Middleware::$floor["organization"], Middleware::$floor["client"]);
		$this->redirectToRoute("/organization/" . Middleware::$floor["organization"]->getId() . "/clients");
	}

	#[Route("/organization/{OrganizationId}/clients", name: "clients", methods: ["GET", "POST"])]
	#[Middleware(PermissionMiddleware::class, "has", options: "manage_client")]
	public function getAll(ClientService $clientService): Response
	{
		$response = new Response();

		// gets clients
		$clients = $clientService->getAll(Middleware::$floor["organization"]);

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
			"/organization/{OrganizationId}/client/{ClientId}",
			name: "app_client_update",
			methods: ["GET", "POST"]
		)
	]
	#[Middleware(UserCanUpdateClientMiddleware::class, "exist", output: "client")]
	public function update(Request $request, ClientService $clientService): Response
	{
		$response = new Response();

		$form = $this->createForm(UpdateClientForm::class, Middleware::$floor["client"]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Client */
			$client = $form->getData();
			try {
				$clientService->update(Middleware::$floor["organization"], $client);
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
