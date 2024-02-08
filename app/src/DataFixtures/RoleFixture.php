<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// local imports
use App\Entity\Role;
use App\DataFixtures\UserFixture;
use App\DataFixtures\OrganizationFixture;

class RoleFixture extends Fixture implements DependentFixtureInterface
{
	public function load(ObjectManager $manager): void
	{
		$roleManager = new Role();
		$roleManager->addUser($this->getReference(UserFixture::USER_MANAGER));
		$roleManager->setOrganization($this->getReference(OrganizationFixture::ORGANIZATION));
		$roleManager->setName("MANAGER");
		$roleManager->setManageOrg(true); // true
		$roleManager->setManageUser(true); // true
		$roleManager->setManageClient(false);
		$roleManager->setWriteDevis(false);
		$roleManager->setWriteFactures(false);
		$roleManager->setManageService(false);
		$roleManager->setReadDevis(false);
		$roleManager->setReadFactures(false);
		$manager->persist($roleManager);

		$roleFacManager = new Role();
		$roleFacManager->addUser($this->getReference(UserFixture::FAC_MANAGER));
		$roleFacManager->setOrganization($this->getReference(OrganizationFixture::ORGANIZATION));
		$roleFacManager->setName("FAC_MANAGER");
		$roleFacManager->setManageOrg(false);
		$roleFacManager->setManageUser(false);
		$roleFacManager->setManageClient(false);
		$roleFacManager->setWriteDevis(false);
		$roleFacManager->setWriteFactures(true); // true
		$roleFacManager->setManageService(false);
		$roleFacManager->setReadDevis(true); // true
		$roleFacManager->setReadFactures(true); // true
		$manager->persist($roleFacManager);

		$manager->flush();
	}

	public function getDependencies()
	{
		return [UserFixture::class, OrganizationFixture::class];
	}
}
