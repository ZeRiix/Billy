<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;

// local imports
use App\Entity\User;
use App\Entity\UserRegister;
use App\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}

	/**
	 * @param string $email
	 * @return User|null
	 */
	public function getByEmail(string $email): User|null
	{
		return $this->findOneBy(["email" => $email]);
	}

	/**
	 * @param array $data
	 * @return User
	 */
	public function create(UserRegister $userRegister): User
	{
		$user = new User();

		$user
			->setFirstName($userRegister->getFirstName())
			->setName($userRegister->getName())
			->setEmail($userRegister->getEmail())
			->setPassword($userRegister->getPassword());

		$this->save($user);

		return $user;
	}
}
