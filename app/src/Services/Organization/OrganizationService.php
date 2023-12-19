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
use App\Repository\UserRepository;
use App\Services\MailService;
use App\Repository\InviteOrganizationRepository;

class OrganizationService
{
	private OrganizationRepository $organizationRepository;

	private RoleRepository $roleRepository;

	private UserRepository $userRepository;

	private InviteOrganizationRepository $inviteOrganizationRepository;

	public function __construct(
		OrganizationRepository $organizationRepository,
		RoleRepository $roleRepository,
		UserRepository $userRepository,
		InviteOrganizationRepository $inviteOrganizationRepository
	) {
		$this->organizationRepository = $organizationRepository;
		$this->roleRepository = $roleRepository;
		$this->userRepository = $userRepository;
		$this->inviteOrganizationRepository = $inviteOrganizationRepository;
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
		$isOwner = $this->roleRepository->findOneBy(["name" => "OWNER"]);
		if ($isOwner && $isOwner->getUsers()->contains($user)) {
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
		// set user in organization
		$organization->addUser($user);
		// set owner for organization
		$this->roleRepository->setOwner($user, $organization);
		// save organization
		$this->organizationRepository->save($organization);
	}

	public static function constructNameForOrganization(array $data): string
	{
		$organizationInfos = $data["etablissement"]["uniteLegale"];
		return $organizationInfos["denominationUniteLegale"];
	}

	public static function constructAddressForOrganization(array $data): string
	{
		$organizationInfos = $data["etablissement"]["adresseEtablissement"];
		$streetNumber = $organizationInfos["numeroVoieEtablissement"];
		$streetType = strtolower($organizationInfos["typeVoieEtablissement"]);
		$streetWording = $organizationInfos["libelleVoieEtablissement"];
		$cityWording = $organizationInfos["libelleCommuneEtablissement"];

		return $streetNumber . " " . $streetType . " " . $streetWording . ", " . $cityWording;
	}

	public static function getResponseForSiret(string $siret): CurlResponse
	{
		$apiSiretUrl = $_ENV["API_SIRENE_URI"] . $siret;
		$client = HttpClient::create();
		$bearerToken = $_ENV["API_SIRENE_TOKEN"];
		$headers = [
			"headers" => [
				"Authorization" => "Bearer " . $bearerToken,
			],
		];
		$response = $client->request("GET", $apiSiretUrl, $headers);

		if ($response->getStatusCode() !== Response::HTTP_OK) {
			throw new \Exception("Une erreur est survenue lors de la vérification du siret.");
		}

		return $response;
	}

	public static function checkSiret(CurlResponse $response): bool
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

	public function invite(string $email, Organization $organization): void
	{
		// check if user is already registered
		$user = $this->userRepository->getByEmail($email);
		if ($user === null) {
			throw new \Exception("L'utilisateur n'est pas encore inscrit.");
		}
		// check if user is already in organization
		$userInOrg = $this->checkIsInOrganization($user, $organization);
		if ($userInOrg) {
			throw new \Exception("L'utilisateur est déjà dans l'organisation.");
		}
		// save invitation
		$this->inviteOrganizationRepository->create($organization, $user);

		// send invitation email to user
		$mail = new MailService();
		$mail->send(
			$email,
			"Invitation à rejoindre l'organisation " . $organization->getName(),
			"Bonjour, vous avez été invité à rejoindre l'organisation " .
				$organization->getName() .
				"." .
				"rejoint l'organisation en cliquant sur le lien suivant : " .
				$_ENV["HOST"] .
				"/organization/" .
				$organization->getId() .
				"/" .
				$user->getId() .
				"/join"
		);
	}

	public function join(string $org, string $user): void
	{
		// get the organization
		$organization = $this->organizationRepository->findOneById($org);
		// get the user
		$user = $this->userRepository->findOneById($user);
		// check if user is already in organization
		$userInOrg = $this->checkIsInOrganization($user, $organization);
		if (!$userInOrg) {
			throw new \Exception("L'utilisateur n'est pas dans l'organisation.");
		}
		// check if user has an invitation
		$invite = $this->inviteOrganizationRepository->getInviteOrganizationByOrganizationAndUser(
			$organization,
			$user
		);
		if ($invite === null) {
			throw new \Exception("L'utilisateur n'a pas d'invitation pour cette organisation.");
		}

		// add user to organization
		$organization->addUser($user);
		$this->organizationRepository->save($organization);
	}

	public function leave(string $userId, Organization $organization): void
	{
		// get the user
		$user = $this->userRepository->findOneById($userId);
		// check if user is in organization
		$userInOrg = $this->checkIsInOrganization($user, $organization);
		if (!$userInOrg) {
			throw new \Exception("L'utilisateur n'est pas dans l'organisation.");
		}
		// check if user is owner of organization
		$owner = $organization->getCreatedBy();
		if ($owner->getId() === $user->getId()) {
			throw new \Exception(
				"Vous ne pouvez pas quitter l'organisation en tant que propriétaire."
			);
		}
		// remove user from organization
		$organization->removeUser($user);
		$this->organizationRepository->save($organization);
	}

	public function checkIsInOrganization(User $user, Organization $organization): bool
	{
		$users = $organization->getUsers();
		foreach ($users as $userInOrganization) {
			if ($userInOrganization->getId() === $user->getId()) {
				return true;
			}
		}
		return false;
	}
}
