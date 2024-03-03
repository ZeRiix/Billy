<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;

// local imports
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Entity\UserRegister;

class UserRegisterRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, UserRegister::class);
	}

	/**
	 * @param string $email
	 * @return UserRegister|null
	 */
	public function getByEmail(string $email)
	{
		return $this->findOneBy(["email" => $email]);
	}

	/**
	 * @param array $data
	 * @return UserRegister
	 */
	public function create(array $data)
	{
		$user = new UserRegister();

		$user->setFirstName($data["firstname"]);
		$user->setName($data["name"]);
		$user->setEmail($data["email"]);
		$user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT)); // define password hash in .env

		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->flush();

		return $user;
	}
}
