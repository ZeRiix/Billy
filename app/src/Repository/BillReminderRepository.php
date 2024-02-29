<?php

namespace App\Repository;

use App\Entity\BillReminder;
use App\Repository\Traits\SaveTrait;
use App\Repository\Traits\DeleteTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BillReminderRepository extends ServiceEntityRepository
{
	use SaveTrait;
	use DeleteTrait;

	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, BillReminder::class);
	}
}
