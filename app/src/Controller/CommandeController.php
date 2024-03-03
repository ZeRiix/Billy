<?php

namespace App\Controller;

//local
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//services
use App\Services\Commande\CommandeService;
//entity
use App\Entity\Commande;
use App\Entity\Organization;
use App\Entity\Devis;
use App\Entity\DeviStatus;
use App\Entity\Service;
//form
use App\Form\CreateCommandeForm;
use App\Form\EditCommandeForm;
use App\Security\Voter\CommandeVoter;

class CommandeController extends AbstractController
{
	#[
		Route(
			"/organization/{organization}/devis/{devis}/commande",
			name: "app_create_commande",
			methods: ["GET", "POST"]
		)
	]
	public function create(Request $request, Devis $devis, CommandeService $commandeService): Response
	{
		$organization = $devis->getOrganization();
		if (!$this->isGranted(CommandeVoter::CREATE, $devis)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour créer une commande.");
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		if ($devis->getStatus() !== DeviStatus::EDITING) {
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		$response = new Response();
		$commande = new Commande();
		$form = $this->createForm(CreateCommandeForm::class, $commande, [
			"organization_id" => $organization->getId(),
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$commandeService->create($commande, $devis);
				$this->addFlash("success", "La commande a bien été créée.");
				return $this->redirectToRoute("app_update_devis", [
					"organization" => $organization->getId(),
					"devis" => $devis->getId(),
				]);
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		$services = [];
		foreach ($organization->getServices() as $s) {
			if (!$s->getIsArchived()) {
				$services[] = $s;
			}
		}

		return $this->render(
			"commande/commande.create.html.twig",
			[
				"form" => $form->createView(),
				"organization" => $organization,
				"devis" => $devis,
				"isUpdate" => false,
				"services" => $services,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/devis/{devis}/commande/{commande}",
			name: "app_update_commande",
			methods: ["GET", "POST"]
		)
	]
	public function update(Request $request, Commande $commande, CommandeService $commandeService)
	{
		$devis = $commande->getDevis();
		$organization = $devis->getOrganization();
		if (!$this->isGranted(CommandeVoter::UPDATE, $commande)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour modifier une commande.");
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		if ($devis->getStatus() !== DeviStatus::EDITING) {
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		$response = new Response();
		$form = $this->createForm(CreateCommandeForm::class, $commande, [
			"organization_id" => $organization->getId(),
		]);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$commandeService->update($commande);
				$this->addFlash("success", "La commande a bien été modifiée.");
				return $this->redirectToRoute("app_update_devis", [
					"organization" => $organization->getId(),
					"devis" => $devis->getId(),
				]);
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		$services = [];
		foreach ($organization->getServices() as $s) {
			if (!$s->getIsArchived()) {
				$services[] = $s;
			}
		}

		return $this->render(
			"commande/commande.create.html.twig",
			[
				"form" => $form->createView(),
				"organization" => $organization,
				"devis" => $devis,
				"commande" => $commande,
				"isUpdate" => true,
				"services" => $services,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/devis/{devis}/commande/{commande}/delete",
			name: "app_delete_commande",
			methods: ["GET"]
		)
	]
	public function delete(Commande $commande, CommandeService $commandeService)
	{
		$devis = $commande->getDevis();
		$organization = $devis->getOrganization();
		if (!$this->isGranted(CommandeVoter::DELETE, $commande)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour supprimer une commande.");
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		if ($devis->getStatus() !== DeviStatus::EDITING) {
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		try {
			$commandeService->delete($commande);
			$this->addFlash("success", "La commande a bien été supprimée.");
		} catch (\Exception $e) {
			$this->addFlash("error", $e->getMessage());
		}

		return $this->redirectToRoute("app_update_devis", [
			"organization" => $organization->getId(),
			"devis" => $devis->getId(),
		]);
	}
}
