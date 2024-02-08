<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// local imports
use App\Entity\Devis;
use App\DataFixtures\ClientFixture;
use App\DataFixtures\CommandFixture;
use App\DataFixtures\OrganizationFixture;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\DeviStatus;

class DevisFixture extends Fixture implements DependentFixtureInterface
{
	public const DEVIS_1 = "devis_1";
	public const DEVIS_2 = "devis_2";
	public const DEVIS_3 = "devis_3";

	public function load(ObjectManager $manager)
	{
		$refDevis = [DevisFixture::DEVIS_1, DevisFixture::DEVIS_2, DevisFixture::DEVIS_3];

		$clientRepo = $manager->getRepository(Client::class);
		$faker = Faker::create("fr_FR");
		for ($i = 0; $i < 3; $i++) {
			$devis = new Devis();
			$devis->setName($faker->sentence(3));
			$devis->setDescription($faker->text(200));
			$devis->setOrganization($this->getReference(OrganizationFixture::ORGANIZATION));
			$devis->setClient($clientRepo->findRandomClient());
			$devis->setStatus(DeviStatus::EDITING);
			$this->addReference($refDevis[$i], $devis);
			$manager->persist($devis);
		}

		$manager->flush();
	}

	public function getDependencies()
	{
		return [ClientFixture::class, OrganizationFixture::class];
	}
}
