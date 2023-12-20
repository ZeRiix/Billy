<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class OrganizationRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Organization::class);
	}

	public function findOneByName($value): ?Organization
	{
		return $this->findOneBy(["name" => $value]);
	}

	public function findUserInOrganization(User $user): ?Organization
	{
		return $this->findOneBy(["users" => $user]);
	}

	public function findOneBySiret($value): ?Organization
	{
		return $this->findOneBy(["siret" => $value]);
	}

	public function organizationContainsUser(Organization $organization, User $user): bool
	{
		$conn = $this->getEntityManager()->getConnection();
		$sql =
			"SELECT user_id FROM user_organizations WHERE user_id = :user_id AND organization_id = :organization_id";
		//die($sql);
		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"user_id" => $user->getId(),
			"organization_id" => $organization->getId(),
		]);
		$result = $res->fetchAllAssociative();

		//die(var_dump($result));

		if (count($result) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function findById($value): ?Organization
	{
		return $this->findOneBy(["id" => $value]);
	}
}
