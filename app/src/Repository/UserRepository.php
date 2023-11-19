<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;

// local imports
use App\Entity\User;
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
	public function create(array $data): User
	{
		$user = new User();

		$user->setFirstName($data["firstname"]);
		$user->setName($data["name"]);
		$user->setEmail($data["email"]);
		$user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT)); // define password hash in .env

		$this->save($user);

		return $user;
	}
}
