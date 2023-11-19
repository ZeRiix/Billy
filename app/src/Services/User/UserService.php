<?php

namespace App\Services\User;

use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Repository\UserRepository;
use App\Entity\User;

class UserService
{
	private UserRepository $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @return User[]|null
	 */
	public function getAll(): array
	{
		/** @var User[] $users */
		$users =  $this->userRepository->getAll();

		$usersArray = [];
		foreach ($users as $user) { // horrible
			$usersArray[] = [
				'id' => $user->getId(),
				'firstname' => $user->getFirstName(),
				'name' => $user->getName(),
				'email' => $user->getEmail(),
				'password' => $user->getPassword(),
				'created_at' => $user->getCreatedAt(),
				'updated_at' => $user->getUpdatedAt()
			]; // resoudre bug get entity
		}
		return $usersArray;
	}

	/**
	 * @param string $id
	 * @return User|null
	 */
	public function getById(string $id): array
	{
		/** @var User $user */
		$user =  $this->userRepository->getById($id);
		if (!$user) {
			throw new \Exception('User not found', Response::HTTP_NOT_FOUND);
		}

		return [ // horrible
			'id' => $user->getId(),
			'firstname' => $user->getFirstName(),
			'name' => $user->getName(),
			'email' => $user->getEmail(),
			'password' => $user->getPassword(),
			'created_at' => $user->getCreatedAt(),
			'updated_at' => $user->getUpdatedAt()
		]; // resoudre bug get entity
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function deleteById(string $id): void
	{
		$this->userRepository->deleteById($id);
	}
}