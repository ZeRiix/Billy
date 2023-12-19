<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Middleware\Middleware;
use App\Middleware\PermissionMiddleware;
use App\Services\Client\ClientService;

class ClientController extends MiddlewareController
{
	#[Route("/client/{OrganizationId}", name: "app_client", methods: ["GET", "POST"])]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_client"],
			redirectTo: "/dashboard"
		)
	]
	public function create(Request $request, ClientService $clientService): Response
	{
		$response = new Response();

		$form = $this->createForm(CreateClientForm::class, null);
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
			"client/index.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/client/{OrganizationId}/delete", name: "client_delete", methods: ["GET", "POST"])]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_client"],
			redirectTo: "/dashboard"
		)
	]
	public function delete(Request $request, ClientService $clientService): Response
	{
		$response = new Response();

		// gets clients
		$clients = $clientService->getAll(Middleware::$floor["organization"]);

		$form = $this->createForm(DeleteClientForm::class, null, [
			"clients" => $clients,
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Client */
			$client = $form->getData();
			try {
				$clientService->delete(Middleware::$floor["organization"], $client);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "Le client a bien été supprimé.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"client/delete.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}

	#[Route("/clients/{OrganizationId}", name: "clients", methods: ["GET", "POST"])]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_client"],
			redirectTo: "/dashboard"
		)
	]
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

	#[Route("/client/{OrganizationId}/{ClientId}", name: "client", methods: ["GET", "POST"])]
	#[
		Middleware(
			PermissionMiddleware::class,
			"exist",
			options: ["permission" => "manage_client"],
			redirectTo: "/dashboard"
		)
	]
	public function get(Request $request, ClientService $clientService): Response
	{
		$response = new Response();
		// get client
		$client = $clientService->get(Middleware::$floor["organization"], $request->get("ClientId"));

		return $this->render(
			"client/client.html.twig",
			[
				"client" => $client,
			],
			$response
		);
	}
}
