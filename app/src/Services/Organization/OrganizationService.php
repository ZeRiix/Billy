<?php

namespace App\Services\Organization;

use App\Entity\Organization;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Component\HttpFoundation\Response;

class OrganizationService
{
	private OrganizationRepository $organizationRepository;

	private EntityManagerInterface $manager;

	private const URL_API_SIRET = "https://api.insee.fr/entreprises/sirene/V3/siret/";

	public function __construct(
		OrganizationRepository $organizationRepository,
		EntityManagerInterface $manager
	) {
		$this->organizationRepository = $organizationRepository;
		$this->manager = $manager;
	}

	public function createOrganization(Organization $organization, User $user)
	{
		//check if an organization exist with siret
		$organizationBySiret = $this->getOrganizationBySiret($organization->getSiret());

		//response of http request to gouv api to get campany infos
		/** @var CurlResponse */
		$responseForSiret = $this->getResponseForSiret($organization->getSiret());
		$responseContent = $responseForSiret->toArray();
		//boolean to check if siret exist
		$isValidSiret = $this->checkSiret($responseForSiret);

		if ($organizationBySiret) {
			throw new \Exception("Une organisation avec ce siret existe déjà.");
		} elseif ($isValidSiret === false) {
			throw new \Exception("Veuillez vérifier votre siret.");
		}

		//setting address automatically with info of api sirene
		$this->setAddressForOrganization($organization, $responseContent);

		//setting name automatically with info of api sirene
		$this->setNameForOrganization($organization, $responseContent);

		//create role owner for organization with the user who create it
		$this->createOwnerRoleForOrganization($organization, $user);

		$this->manager->persist($organization);
		$this->manager->flush($organization);
	}

	public function getOrganizationByName(string $name): ?Organization
	{
		return $this->organizationRepository->findOneByName($name);
	}

	public function getOrganizationBySiret(string $siret): ?Organization
	{
		return $this->organizationRepository->findOneBySiret($siret);
	}

	private function createOwnerRoleForOrganization(Organization $organization, User $user): void
	{
		/** @var Role */
		//create new role
		$ownerRole = new Role();
		//set name as 'OWNER'
		$ownerRole->setName("OWNER");
		//set all perms at true
		$ownerRole->initOwner();
		//set created organization at role
		$ownerRole->setOrganization($organization);
		//add user that created the organization at role
		$ownerRole->addUser($user);

		$this->manager->persist($user);
		$this->manager->persist($ownerRole);
		$this->manager->flush();
	}

	private function setNameForOrganization(Organization $organization, array $data): void
	{
		$organizationInfos = $data["etablissement"]["uniteLegale"];
		$organizationName = $organizationInfos["denominationUniteLegale"];

		$organization->setName($organizationName);
	}

	private function setAddressForOrganization(Organization $organization, array $data): void
	{
		$organizationInfos = $data["etablissement"]["adresseEtablissement"];
		$streetNumber = $organizationInfos["numeroVoieEtablissement"];
		$streetType = strtolower($organizationInfos["typeVoieEtablissement"]);
		$streetWording = $organizationInfos["libelleVoieEtablissement"];
		$cityWording = $organizationInfos["libelleCommuneEtablissement"];
		$organizationAddress =
			$streetNumber . " " . $streetType . " " . $streetWording . ", " . $cityWording;
		$organization->setAddress($organizationAddress);
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
}
