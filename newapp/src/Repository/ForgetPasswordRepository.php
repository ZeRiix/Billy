<?php

namespace App\Repository;

use App\Entity\ForgetPassword;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

// local imports
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class ForgetPasswordRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ForgetPassword::class);
	}

	public function getByUser(User $user): ?ForgetPassword
	{
		return $this->findOneBy(["user" => $user]);
	}
}
