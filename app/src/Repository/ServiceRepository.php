<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\Service;
use App\Repository\Traits\SaveTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 *
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
	use SaveTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Service::class);
	}

	/**
	 * @return Service Returns an array of Service objects
	 */
	public function findByName(Organization $organization, string $name): ?Service
	{
		return $this->findOneBy(["organization" => $organization, "name" => $name]);
	}

	public function findRandomService()
	{
		$services = $this->findAll();
		$randomService = $services[array_rand($services)];
		return $randomService;
	}

	//    public function findOneBySomeField($value): ?Service
	//    {
	//        return $this->createQueryBuilder('s')
	//            ->andWhere('s.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
