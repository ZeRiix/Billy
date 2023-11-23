<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends OrganizationEntityRepository<Organization>
 *
 * @method Organization|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organization|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organization[]    findAll()
 * @method Organization[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizationRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Organization::class);
	}

	//    /**
	//     * @return Organization[] Returns an array of Organization objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('s')
	//            ->andWhere('s.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('s.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }

	public function findOneByName($value): ?Organization
	{
		return $this->createQueryBuilder("s")
			->andWhere("s.name = :val")
			->setParameter("val", $value)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findOneBySiret($value): ?Organization
	{
		return $this->createQueryBuilder("s")
			->andWhere("s.siret = :val")
			->setParameter("val", $value)
			->getQuery()
			->getOneOrNullResult();
	}
}
