<?php

namespace App\Controller;

use App\Services\Organization\OrganizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Middleware\Middleware;
use App\Middleware\SelfUserMiddleware;

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

	#[Route("/dashboard/organizations", name: "dashboard_organizations", methods: ["GET"])]
	#[Middleware(SelfUserMiddleware::class, "exist", output: "user", redirectTo: "/login")]
	public function organizations(OrganizationService $organizationService): Response
	{
		$response = new Response();
		$organizations = [];
		$organizations = $organizationService->getAllOrganizationsByUser(
			Middleware::$floor["user"]
		);
		$response->setStatusCode(Response::HTTP_OK);
		return $this->render(
			"dashboard/organizations.html.twig",
			[
				"organizations" => $organizations,
			],
			$response
		);
	}
}
