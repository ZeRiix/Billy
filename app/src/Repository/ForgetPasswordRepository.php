<?php

namespace App\Repository;

use App\Entity\ForgetPassword;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

// local imports
use App\Repository\BaseRepository;
use App\Entity\UserRegister;

class ForgetPasswordRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, ForgetPassword::class);
	}

	public function getByUser(User $user): ?ForgetPassword
	{
		return $this->findOneBy(["user_id" => $user->getId()]);
	}
}
