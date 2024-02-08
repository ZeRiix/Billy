<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
// local imports
use App\Entity\Commande;
use App\DataFixtures\DevisFixture;
use App\DataFixtures\CommandFixture;
use App\Entity\Devis;

class CalculDevisFixture extends Fixture implements DependentFixtureInterface
{
	public function load(ObjectManager $manager)
	{
		$devisRepo = $manager->getRepository(Devis::class);
		/** @var Devis[] $devis */
		$devis = $devisRepo->findAll();
		foreach ($devis as $devis) {
			$montant = 0;
			foreach ($devis->getCommandes() as $commande) {
				$montant += $commande->getMontant();
			}
			$devis->setTotalHt($montant);
			$devis->setTotalTtc($montant * 1.2); // 20%
			$manager->persist($devis);
		}
		$manager->flush();
	}

	public function getDependencies()
	{
		return [CommandFixture::class, DevisFixture::class];
	}
}
