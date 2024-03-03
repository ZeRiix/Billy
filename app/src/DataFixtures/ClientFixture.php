<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// local imports
use App\Entity\Client;
use App\DataFixtures\OrganizationFixture;
use App\Services\Client\ClientService;

class ClientFixture extends Fixture implements DependentFixtureInterface
{
	private ClientService $clientService;

	public function __construct(ClientService $clientService)
	{
		$this->clientService = $clientService;
	}

	public function load(ObjectManager $manager): void
	{
		$faker = Faker::create("fr_FR");
		for ($i = 0; $i < 4; $i++) {
			// personal client
			$client = new Client();
			$client->setName($faker->lastName());
			$client->setFirstname($faker->firstName());
			$client->setAddress($faker->address());
			$client->setEmail($faker->email());
			$client->setPhone("1234567890");
			$client->setOrganization($this->getReference(OrganizationFixture::ORGANIZATION));
			$manager->persist($client);
		}
		$manager->flush();

		$sirets = [
			42062477700199, // canal
			44163946500018, // renault
			38012986646850, // orange
			39747182200114, // disney
			67205008501061, // carrefour
			91943489400029, // l'oreal
		];
		for ($i = 4; $i < 10; $i++) {
			$client = new Client();
			$client->setSiret($sirets[$i - 4]);
			$client->setEmail($faker->companyEmail());
			$client->setPhone("1234567890");
			$this->clientService->create($this->getReference(OrganizationFixture::ORGANIZATION), $client);
		}
	}

	public function getDependencies()
	{
		return [OrganizationFixture::class];
	}
}
