<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// local imports
use App\Entity\Commande;
use App\DataFixtures\ServiceFixture;
use App\DataFixtures\DevisFixture;
use App\Entity\Service;

class CommandFixture extends Fixture implements DependentFixtureInterface
{
	public function load(ObjectManager $manager)
	{
		$refDevis = [DevisFixture::DEVIS_1, DevisFixture::DEVIS_2, DevisFixture::DEVIS_3];

		$faker = Faker::create("fr_FR");
		$serviceRepo = $manager->getRepository(Service::class);
		for ($i = 0; $i < 10; $i++) {
			$service = $serviceRepo->findRandomService();
			$commande = new Commande();
			$commande->setName($faker->sentence(3));
			$commande->setDescription($faker->text(200));
			$commande->setService($service);
			$commande->setUnitPrice($service->getUnitPrice());
			$quantity = $faker->randomNumber(2);
			$commande->setQuantity($quantity);
			$commande->setMontant($service->getUnitPrice() * $quantity);
			$commande->setDevis($this->getReference($refDevis[array_rand($refDevis)]));
			$manager->persist($commande);
		}

		$manager->flush();
	}

	public function getDependencies()
	{
		return [ServiceFixture::class, DevisFixture::class];
	}
}
