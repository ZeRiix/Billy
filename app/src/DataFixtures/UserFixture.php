<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// local imports
use App\Entity\User;

class UserFixture extends Fixture
{
	public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
	{
	}
	public const OWNER = "owner";
	public const USER_MANAGER = "userManager";
	public const FAC_MANAGER = "facManager";

	public function load(ObjectManager $manager): void
	{
		$faker = Faker::create("fr_FR");
		$users = [
			[
				"email" => "owner@owner.fr",
				"first_name" => "user",
				"last_name" => "user",
				"reference" => self::OWNER,
			],
			[
				"email" => "user.manager@user.fr",
				"first_name" => "manager",
				"last_name" => "manager",
				"reference" => self::USER_MANAGER,
			],
			[
				"email" => "fac.manager@user.fr",
				"first_name" => "facManager",
				"last_name" => "facManager",
				"reference" => self::FAC_MANAGER,
			],
		];

		foreach ($users as $user) {
			$u = new User();

			$u->setEmail($user["email"]);
			$u->setFirstName($user["first_name"]);
			$u->setName($user["last_name"]);
			$u->setPassword($this->passwordHasher->hashPassword($u, "Respons11@"));
			$manager->persist($u);
			$this->addReference($user["reference"], $u);
		}

		for ($i = 0; $i < 10; $i++) {
			$user = new User();
			$user->setEmail($faker->email);
			$user->setFirstName($faker->firstName);
			$user->setName($faker->lastName);
			$user->setPassword($this->passwordHasher->hashPassword($user, "Respons11@"));
			$manager->persist($user);
		}
		$manager->flush();
	}
}
