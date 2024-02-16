<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Repository\Traits\SaveTrait;
use App\Entity\Organization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends FactureEntityRepository<Facture>
 *
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
	use SaveTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Facture::class);
	}

	public function genChrono(Organization $organization): int
	{
		$conn = $this->_em->getConnection();
		$sql = "SELECT count(*) FROM facture WHERE organization_id = :organization_id";

		$conn->prepare($sql);
		$res = $conn->executeQuery($sql, [
			"organization_id" => $organization->getId(),
		]);

		return $res->fetchAllAssociative()[0]["count"] + 1;
	}
}
