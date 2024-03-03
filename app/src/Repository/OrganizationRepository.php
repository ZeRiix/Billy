<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\User;
use App\Repository\Traits\SaveTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Organization>
 *
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findAll()
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationRepository extends ServiceEntityRepository
{
	use SaveTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Organization::class);
	}

	public function findOneByName(string $name): ?Organization
	{
		return $this->createQueryBuilder("o")
			->andWhere("o.name = :name")
			->setParameter("name", $name)
			->getQuery()
			->getOneOrNullResult();
	}

	public function userCanCreateOrganization(User $user): bool
	{
		return $this->findOneBy(["createdBy" => $user]) === null;
	}

	public function organizationContainsUser(Organization $organization, User $user): bool
	{
		$conn = $this->_em->getConnection();
		$sql =
			"SELECT user_id FROM user_organizations WHERE user_id = :user_id AND organization_id = :organization_id";
		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"user_id" => $user->getId(),
			"organization_id" => $organization->getId(),
		]);
		$result = $res->fetchAllAssociative();

		return count($result) > 0;
	}

	public function getUsersByOrganization(Organization $organization)
	{
		return $this->find(["id" => $organization->getId()])->getUsers();
	}

	public function statsService(Organization $organization, string $from, string $to)
	{
		$conn = $this->_em->getConnection();
		$sql = "SELECT
			service.name as name,
			service.id as id,
			SUM(commande.quantity) as quantity
		FROM service
		INNER JOIN commande on commande.service_id = service.id
		WHERE 
			service.organization_id = :organization_id AND
			TO_DATE(:from, 'YYYY-MM-DD')::timestamp <= commande.updated_at::timestamp AND
			TO_DATE(:to, 'YYYY-MM-DD')::timestamp >= commande.updated_at::timestamp
		GROUP BY service.id, service.name
		ORDER BY quantity desc
		LIMIT 15
		";

		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"organization_id" => $organization->getId(),
			"from" => $from,
			"to" => $to,
		]);
		$result = $res->fetchAllAssociative();

		return $result;
	}

	public function statsStatusDevis(Organization $organization, string $from, string $to)
	{
		$conn = $this->_em->getConnection();
		$sql = "SELECT
			status,
			COUNT(*) as count
		FROM devis
		WHERE 
			organization_id = :organization_id AND
			TO_DATE(:from, 'YYYY-MM-DD')::timestamp <= updated_at::timestamp AND
			TO_DATE(:to, 'YYYY-MM-DD')::timestamp >= updated_at::timestamp
		GROUP BY status
		";

		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"organization_id" => $organization->getId(),
			"from" => $from,
			"to" => $to,
		]);
		$result = $res->fetchAllAssociative();

		return $result;
	}

	public function statsCompletedDevis(Organization $organization, string $from, string $to)
	{
		$conn = $this->_em->getConnection();
		$sql = "SELECT
			status,
			updated_at::date as date,
			COUNT(*)
		FROM devis
		WHERE 
			organization_id = :organization_id AND
			TO_DATE(:from, 'YYYY-MM-DD')::timestamp <= updated_at::timestamp AND
			TO_DATE(:to, 'YYYY-MM-DD')::timestamp >= updated_at::timestamp AND
			status = 'completed'
		GROUP BY date, status
		";

		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"organization_id" => $organization->getId(),
			"from" => $from,
			"to" => $to,
		]);
		$result = $res->fetchAllAssociative();

		return $result;
	}

	//    /**
	//     * @return Organization[] Returns an array of Organization objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('o')
	//            ->andWhere('o.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('o.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }

	//    public function findOneBySomeField($value): ?Organization
	//    {
	//        return $this->createQueryBuilder('o')
	//            ->andWhere('o.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
