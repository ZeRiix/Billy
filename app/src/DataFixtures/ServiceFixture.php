<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory as Faker;
// local imports
use App\Entity\Service;
use App\DataFixtures\OrganizationFixture;

class ServiceFixture extends Fixture implements DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$faker = Faker::create("fr_FR");
		for ($i = 0; $i < 10; $i++) {
			$service = new Service();
			$service->setName($faker->sentence(3));
			$service->setDescription($faker->text(200));
			$service->setOrganization($this->getReference(OrganizationFixture::ORGANIZATION));
			$service->setUnitPrice($faker->randomFloat(2, 0, 1000));
			$service->setIsArchived(false);
			$manager->persist($service);
		}

		$manager->flush();
	}

	public function getDependencies()
	{
		return [OrganizationFixture::class];
	}
}
