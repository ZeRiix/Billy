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

	public function findOneByName($value): ?Organization
	{
		return $this->findOneBy(["name" => $value]);
	}

	public function findOneBySiret($value): ?Organization
	{
		return $this->findOneBy(["siret" => $value]);
	}
}