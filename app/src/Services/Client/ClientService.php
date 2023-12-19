<?php

namespace App\Services\Client;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Entity\Client;
use App\Entity\Organization;
use App\Repository\ClientRepository;
use App\Repository\OrganizationRepository;
use App\Services\Organization\OrganizationService;

class ClientService
{
	private ClientRepository $clientRepository;
	private OrganizationRepository $organizationRepository;

	public function __construct(
		ClientRepository $clientRepository,
		OrganizationRepository $organizationRepository
	) {
		$this->clientRepository = $clientRepository;
		$this->organizationRepository = $organizationRepository;
	}

	public function create(Organization $organization, Client $client)
	{
		// check siret is already registered
		if ($this->clientRepository->findOneBySiret($client->getSiret())) {
			throw new \Exception("Un client avec ce siret existe déjà.");
		}
		// check siret is valid and get data client
		$responseForSiret = OrganizationService::getResponseForSiret(
			$client->getSiret()
		)->toArray();

		// set response data to client
		$client->setOrganization($organization);
		$client->setName(OrganizationService::constructNameForOrganization($responseForSiret));
		$client->setAddress(
			OrganizationService::constructAddressForOrganization($responseForSiret)
		);
		$this->clientRepository->save($client);
	}

	public function getAll(Organization $organization): array
	{
		return $this->clientRepository->findBy(["organization" => $organization]);
	}

	public function get(Organization $organization, string $clientId): Client
	{
		// get the client
		$client = $this->clientRepository->findOneBy(["id" => $clientId]);
		if ($client->getOrganization() !== $organization) {
			throw new \Exception("Ce client n'appartient pas à votre organisation.");
		}
		return $client;
	}

	public function delete(Organization $organization, Client $client)
	{
		if ($client->getOrganization() !== $organization) {
			throw new \Exception("Ce client n'appartient pas à votre organisation.");
		}
		$this->clientRepository->delete($client);
	}
}
