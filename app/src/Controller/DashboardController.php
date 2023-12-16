<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController // TODO: need to be connected to access this route
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

	#[Route("/dashboard/organizations", name: "dashboard_organizations")]
	public function organizations(): Response
	{
		return $this->render("dashboard/organizations.html.twig", [
			"controller_name" => "DashboardController",
		]);
	}
}
