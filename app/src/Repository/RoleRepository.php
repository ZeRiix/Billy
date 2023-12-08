<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends RoleEntityRepository<Role>
 *
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Role::class);
	}

	/**
	 * @param array $data<name string, manage_org bool, manage_user bool, manage_client bool, write_devis bool, write_factures bool, organization Organization>
	 * @return Role
	 */
	public function create(array $data): Role
	{
		$role = new Role();
		$role->setName($data["name"]);
		$role->setManageOrg($data["manage_org"]);
		$role->setManageUser($data["manage_user"]);
		$role->setManageClient($data["manage_client"]);
		$role->setWriteDevis($data["write_devis"]);
		$role->setWriteFactures($data["write_factures"]);
		$role->setOrganization($data["organization"]);
		$role->addUser($data["user"]);
		$this->save($role);

		return $role;
	}
}
