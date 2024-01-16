<?php

namespace App\Services\Organization;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Response\CurlResponse;
use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Entity\Organization;
use App\Entity\InviteOrganization;
use App\Entity\User;
use App\Repository\OrganizationRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\MailService;
use App\Repository\InviteOrganizationRepository;

use function Symfony\Component\Clock\now;

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

	public function modify(Organization $organization)
	{
		$this->organizationRepository->save($organization);
	}

	public function create(Organization $organization, User $user) : Organization
	{
		// check siret is already registered
		if ($this->organizationRepository->findOneBySiret($organization->getSiret())) {
			throw new \Exception("Une organisation avec ce siret existe déjà.");
		}
		// check if user is already owner of an organization
		$isOwner = $this->roleRepository->isOwner($user);
		if ($isOwner) {
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
		// return organization
		return $organization;
	}

	public static function constructNameForOrganization(array $data): ?string
	{
		$organizationName = null;
		$uniteLegaleInfos = $data["etablissement"]["uniteLegale"];
		$periodeEtablissementInfos = $data["etablissement"]["periodesEtablissement"][0];

		if ($periodeEtablissementInfos["denominationUsuelleEtablissement"] !== null) {
			$organizationName = $periodeEtablissementInfos["denominationUsuelleEtablissement"];
		} elseif ($uniteLegaleInfos["denominationUniteLegale"] !== null) {
			$organizationName = $uniteLegaleInfos["denominationUniteLegale"];
		} elseif ($uniteLegaleInfos["prenom1UniteLegale"] !== null) {
			$organizationName =
				$uniteLegaleInfos["nomUniteLegale"] . " " . $uniteLegaleInfos["prenom1UniteLegale"];
		} elseif ($uniteLegaleInfos["prenomUsuelUniteLegale"] !== null) {
			$organizationName =
				$uniteLegaleInfos["nomUniteLegale"] . " " . $uniteLegaleInfos["prenomUsuelUniteLegale"];
		}

		return $organizationName;
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
		// check if invited user is already in organization
		/** @var InviteOrganization $invitation */
		$invitation = $this->inviteOrganizationRepository->findOneBy(["user" => $user]);
		if ($invitation) {
			if (date($invitation->getCreatedAt()->getTimestamp()) + 604800 >= date(now()->getTimestamp())) {
				throw new \Exception("Cette utilisateur a déja été invité.");
			}
			// delete last invitation
			$this->inviteOrganizationRepository->delete($invitation);
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
				"<a href=\"" .
				$_SERVER['REQUEST_SCHEME']. "://" . $_SERVER['HTTP_HOST'] .
				"/organization/" .
				$organization->getId() .
				"/user/" .
				$user->getId() .
				"/join\"> Joindre l'organisation </a>",
			false
		);
	}

	public function join(Organization $organization, string $user): void
	{
		// get the user
		$user = $this->userRepository->findOneById($user);
		// check if user is already in organization
		$userInOrg = $this->checkIsInOrganization($user, $organization);
		if ($userInOrg) {
			throw new \Exception("Vous êtes déja dans l'organisation.");
		}
		// check if user has an invitation
		$invite = $this->inviteOrganizationRepository->getInviteOrganizationByOrganizationAndUser(
			$organization,
			$user
		);
		if ($invite === null) {
			throw new \Exception("L'utilisateur n'a pas d'invitation pour cette organisation.");
		}
		// delete invitation
		$this->inviteOrganizationRepository->delete($invite);

		// add user to organization
		$organization->addUser($user);
		$this->organizationRepository->save($organization);
	}

	public function leave(User $user, Organization $organization): void
	{
		// check if user is in organization
		$userInOrg = $this->checkIsInOrganization($user, $organization);
		if (!$userInOrg) {
			throw new \Exception("L'utilisateur n'est pas dans l'organisation.");
		}
		// check if user is owner of organization
		$owner = $organization->getCreatedBy();
		if ($owner->getId() === $user->getId()) {
			throw new \Exception("Vous ne pouvez pas quitter l'organisation en tant que propriétaire.");
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

	public function getCreatedBy(User $user): ?Organization
	{
		return $this->organizationRepository->findOneBy(["createdBy" => $user]);
	}
}