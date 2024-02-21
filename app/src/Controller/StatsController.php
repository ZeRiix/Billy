<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\Security\Voter\OrganizationVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class StatsController extends AbstractController
{
	#[Route("/organization/{organization}/stats", name: "app_stats", methods: ["GET"])]
	public function index(
		Request $request,
		Organization $organization,
		OrganizationRepository $organizationRepository
	) {
		if (!$this->isGranted(OrganizationVoter::VIEW_STATS, $organization)) {
			return $this->redirectToRoute("app_organization_get_id", [
				"organization" => $organization->getId(),
			]);
		}

		$from = $request->query->get("from");
		$to = $request->query->get("to");

		if (\DateTime::createFromFormat("Y-m-d", $from) && \DateTime::createFromFormat("Y-m-d", $to)) {
			$statsService = $organizationRepository->statsService($organization, $from, $to);
			$statsStatusDevis = $organizationRepository->statsStatusDevis($organization, $from, $to);
			$statsCompletedDevis = $organizationRepository->statsCompletedDevis($organization, $from, $to);
		}

		return $this->render("stats/index.html.twig", [
			"statsService" => json_encode($statsService ?? []),
			"statsStatusDevis" => json_encode($statsStatusDevis ?? []),
			"statsCompletedDevis" => json_encode($statsCompletedDevis ?? []),
		]);
	}
}
