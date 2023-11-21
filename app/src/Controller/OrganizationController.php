<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Form\CreateOrganizationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrganizationController extends AbstractController
{
	#[
		Route(
			"/organization",
			name: "app_organization",
			methods: ["GET", "POST"]
		)
	]
	public function create(
		Request $request,
		EntityManagerInterface $manager
	): Response {
		$organization = new Organization();
		$form = $this->createForm(
			CreateOrganizationFormType::class,
			$organization
		);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$organization = $form->getData();
			$url =
				"https://api.insee.fr/entreprises/sirene/V3/siret/" .
				$organization->getSiret();
			$client = HttpClient::create();
			$response = $client->request("GET", $url);
			$responseStatus = $response->getStatusCode();
			if ($responseStatus !== Response::HTTP_OK) {
				$this->addFlash("error", "Veuillez vérifier votre siret.");
			} else {
				$manager->persist($organization);
				$manager->flush($organization);
				$this->addFlash("success", "L'organisation à bien été créée.");
			}
		}

		return $this->render("organization/index.html.twig", [
			"form" => $form->createView(),
		]);
	}
}
