<?php

namespace App\Services\Organization;

use App\Entity\Organization;
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

	public function createOrganization(Organization $organization)
	{
		//check if an organization exist with name
		$organizationByName = $this->getOrganizationByName(
			$organization->getName()
		);
		//check if an organization exist with siret
		$organizationBySiret = $this->getOrganizationBySiret(
			$organization->getSiret()
		);

		//response of http request to gouv api to get campany infos
		/** @var CurlResponse */
		$responseForSiret = $this->getResponseForSiret(
			$organization->getSiret()
		);
		$responseContent = $responseForSiret->toArray();
		//boolean to check if siret exist
		$isValidSiret = $this->checkSiret($responseForSiret);
		if ($organizationByName) {
			throw new \Exception("Une organisation avec ce nom existe déjà.");
		} elseif ($organizationBySiret) {
			throw new \Exception("Une organisation avec ce siret existe déjà.");
		} elseif ($isValidSiret === false) {
			throw new \Exception("Veuillez vérifier votre siret.");
		}

		//setting address automatically with info of api gouv
		$this->setAddressForOrganization($organization, $responseContent);

		//die(var_dump($organization));

		/**TODO add createdBy when i can read token payload */
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

	private function setAddressForOrganization(
		Organization $organization,
		array $data
	): void {
		$organizationInfos = $data["etablissement"]["adresseEtablissement"];
		//die(var_dump($organizationInfos));
		$streetNumber = $organizationInfos["numeroVoieEtablissement"];
		$streetType = strtolower($organizationInfos["typeVoieEtablissement"]);
		$streetWording = $organizationInfos["libelleVoieEtablissement"];
		$cityWording = $organizationInfos["libelleCommuneEtablissement"];
		$organizationAddress =
			$streetNumber .
			" " .
			$streetType .
			" " .
			$streetWording .
			", " .
			$cityWording;
		//die($organizationAddress);
		$organization->setAddress($organizationAddress);
	}

	private function getResponseForSiret(string $siret): CurlResponse
	{
		$apiSiretUrl = self::URL_API_SIRET . $siret;
		$client = HttpClient::create();
		$bearerToken = $_ENV["API_SIRENE_URL"];
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
