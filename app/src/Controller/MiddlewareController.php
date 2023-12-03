<?php

namespace App\Controller;

use App\Middleware\Middleware;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class MiddlewareController extends AbstractController
{
	public function __construct(
		RequestStack $requestStack,
		ManagerRegistry $managerRegistry
	) {
		Middleware::init($requestStack->getMainRequest(), $managerRegistry);
	}
}
