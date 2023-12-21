<?php

namespace App\Controller;

use App\Services\Organization\OrganizationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Middleware\Middleware;
use App\Middleware\OrganizationContainsUserMiddleware;

class DashboardController extends MiddlewareController
{
	#[Route("/dashboard", name: "dashboard")]
	public function index(): Response
	{
		return $this->render("dashboard/index.html.twig", [
			"controller_name" => "DashboardController",
		]);
	}

	#[Route("/dashboard/my-organization", name: "dashboard_my_organization")]
	public function myOrganization(): Response
	{
		return $this->render("dashboard/myOrganization.html.twig", [
			"controller_name" => "DashboardController",
		]);
	}
}
