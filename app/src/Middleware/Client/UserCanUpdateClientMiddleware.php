<?php

namespace App\Service\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
// local imports
use App\Middleware\AbstractMiddleware;
use App\Middleware\Middleware;
use App\Middleware\PermissionMiddleware;
use App\Repository\ClientRepository;

class UserCanUpdateClientMiddleware extends AbstractMiddleware
{
	private ClientRepository $clientRepository;
	private Request $request;

	public function __construct(ClientRepository $clientRepository, Request $request)
	{
		$this->clientRepository = $clientRepository;
		$this->request = $request;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		new Middleware(PermissionMiddleware::class, "has", options: "manage_client");

		// get client
		$client = $this->clientRepository->findOneBy(["id" => $this->request->attributes->get("ClientId")]);

		if ($client->getOrganization() !== Middleware::$floor["organization"]) {
			$this->redirectTo("/organization/" . Middleware::$floor["organization"]->getId() . "/clients");
		}

		return $this->output("exist", $client);
	}
}
