<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// local imports
use App\Entity\Organization;
use App\Services\Organization\OrganizationService;
use App\DataFixtures\UserFixture;

class OrganizationFixture extends Fixture implements DependentFixtureInterface
{
	private OrganizationService $organizationService;

	public function __construct(OrganizationService $organizationService)
	{
		$this->organizationService = $organizationService;
	}

	public const ORGANIZATION = "organization";

	public function load(ObjectManager $manager): void
	{
		$faker = Faker::create("fr_FR");
		$organization = new Organization();
		$organization->setEmail($faker->companyEmail);
		$organization->setPhone("1234567890");
		$organization->setActivity($faker->company);
		$organization->setSiret("39416496600019");
		$organization->addUser($this->getReference(UserFixture::FAC_MANAGER));
		$organization->addUser($this->getReference(UserFixture::USER_MANAGER));
		$this->addReference(self::ORGANIZATION, $organization);
		$this->organizationService->create($organization, $this->getReference(UserFixture::OWNER));
	}

	public function getDependencies()
	{
		return [UserFixture::class];
	}
}
