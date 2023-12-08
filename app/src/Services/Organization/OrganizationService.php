<?php

namespace App\Services\Organization;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Entity\Organization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;

class OrganizationService
{
	private OrganizationRepository $organizationRepository;

	private RoleRepository $roleRepository;

	public function __construct(
		OrganizationRepository $organizationRepository,
		RoleRepository $roleRepository
	) {
		$this->organizationRepository = $organizationRepository;
		$this->roleRepository = $roleRepository;
	}

	public function create(Organization $organization, User $user)
	{
		// check name is already registered
		if ($this->organizationRepository->findOneByName($organization->getName())) {
			throw new \Exception("Une organisation avec ce nom existe déjà.");
		}
		// check siret is already registered
		if ($this->organizationRepository->findOneBySiret($organization->getSiret())) {
			throw new \Exception("Une organisation avec ce siret existe déjà.");
		}
		// check if user is already owner of an organization
		if ($this->roleRepository->findOneBy(["user" => $user, "name" => "OWNER"])) {
			throw new \Exception("Vous êtes déjà propriétaire d'une organisation.");
		}
		// check siret is valid
		$responseForSiret = $this->getResponseForSiret($organization->getSiret());
		if (!$this->checkSiret($responseForSiret)) {
			throw new \Exception("Veuillez vérifier votre siret.");
		}
		$responseForSiret = $responseForSiret->toArray();

		// set name and address for organization
		$organization->setName($this->constructNameForOrganization($responseForSiret));
		// set address for organization
		$organization->setAddress($this->constructAddressForOrganization($responseForSiret));
		// set organization created by
		$organization->setCreatedBy($user);
		// set owner for organization
		$this->roleRepository->setOwner($user, $organization);
		// save organization
		$this->organizationRepository->save($organization);
	}

	private function constructNameForOrganization(array $data): string
	{
		$organizationInfos = $data["etablissement"]["uniteLegale"];
		return $organizationInfos["denominationUniteLegale"];
	}

	private function constructAddressForOrganization(array $data): string
	{
		$organizationInfos = $data["etablissement"]["adresseEtablissement"];
		$streetNumber = $organizationInfos["numeroVoieEtablissement"];
		$streetType = strtolower($organizationInfos["typeVoieEtablissement"]);
		$streetWording = $organizationInfos["libelleVoieEtablissement"];
		$cityWording = $organizationInfos["libelleCommuneEtablissement"];

		return $streetNumber . " " . $streetType . " " . $streetWording . ", " . $cityWording;
	}

	private function getResponseForSiret(string $siret): CurlResponse
	{
		$apiSiretUrl = $_ENV["API_SIRENE_URI"] . $siret;
		$client = HttpClient::create();
		$bearerToken = $_ENV["API_SIRENE_TOKEN"];
		$headers = [
			"headers" => [
				"Authorization" => "Bearer " . $bearerToken,
			],
		];
		return $client->request("GET", $apiSiretUrl, $headers);
	}

	private function checkSiret(CurlResponse $response): bool
	{
		$responseStatus = $response->getStatusCode();
		if ($responseStatus !== Response::HTTP_OK) {
			return false;
		}
		return true;
	}

	public function delete(Organization $organization)
	{
		$this->organizationRepository->delete($organization);
	}
}
