<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Repository\Traits\SaveTrait;
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

	//    /**
	//     * @return Facture[] Returns an array of Facture objects
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

	//    public function findOneBySomeField($value): ?Facture
	//    {
	//        return $this->createQueryBuilder('s')
	//            ->andWhere('s.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
