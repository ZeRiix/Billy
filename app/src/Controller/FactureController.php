<?php

namespace App\Controller;

//local

use App\Entity\Devis;
use App\Entity\DeviStatus;
use App\Entity\Facture;
use App\Entity\Organization;
use App\Security\Voter\OrganizationVoter;
use App\Services\Calculation\CalculationService;
use App\Services\Devis\DevisService;
use App\Services\Facture\FactureService;
use App\Services\Pdf\GeneratePdfService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FactureController extends AbstractController
{
	#[
		Route(
			"/organization/{organization}/quotation/{devis}/bills",
			name: "app_bills_by_devis",
			methods: ["GET"]
		)
	]
	public function index(Devis $devis, DevisService $devisService)
	{
		if (
			!$this->isGranted(OrganizationVoter::READ_FACTURE, $devis->getOrganization()) ||
			$devis->getStatus() != DeviStatus::SIGN
		) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour voir les factures de ce devis ou le devis n'est pas signé."
			);
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $devis->getOrganization()->getId(),
				"devis" => $devis->getId(),
			]);
		}
		$canCreate = empty($devisService->getCommandesNotInFacture($devis));
		return $this->render("facture/factures.html.twig", [
			"bills" => $devis->getFactures(),
			"organization" => $devis->getOrganization(),
			"devis" => $devis,
			"canCreate" => $canCreate,
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/bill",
			name: "app_create_facture",
			methods: ["POST", "GET"]
		)
	]
	public function create(
		Request $request,
		Devis $devis,
		FactureService $factureService,
		DevisService $devisService
	): Response {
		if (
			!$this->isGranted(OrganizationVoter::WRITE_FACTURE, $devis->getOrganization()) ||
			$devis->getStatus() != DeviStatus::SIGN
		) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour créer une facture pour cette Organisation ou le devis n'est pas signé."
			);
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $devis->getOrganization()->getId(),
				"devis" => $devis->getId(),
			]);
		}
		$response = new Response();
		if ($request->getMethod() == "POST") {
			/** @var array $commandes */
			$commandeIds = $request->request->all();
			try {
				//appel du service
				$factureService->create(
					new Facture(),
					$devis->getOrganization(),
					$devis->getClient(),
					$devis,
					$commandeIds
				);

				$this->addFlash("success", "La facture à bien été créee.");
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"facture/facture.create.html.twig",
			[
				"commandes" => $devisService->getCommandesNotInFacture($devis),
				"organization" => $devis->getOrganization(),
				"devis" => $devis,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/bill/{facture}",
			name: "app_facture_get_id",
			methods: ["GET"]
		)
	]
	public function view(Facture $facture)
	{
		$devis = $facture->getDevis();
		//die(var_dump($devis->getOrganization()->getName()));
		if (
			!$this->isGranted(OrganizationVoter::READ_FACTURE, $devis->getOrganization()) ||
			($devis->getStatus() != DeviStatus::SIGN && $devis->getStatus() != DeviStatus::COMPLETED)
		) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour voir les factures de cette Organisation."
			);
			return $this->redirectToRoute("app_bills_by_devis", [
				"organization" => $devis->getOrganization()->getId(),
				"devis" => $devis->getId(),
			]);
		}
		return $this->render("facture/facture.view.html.twig", [
			"bill" => $facture,
			"commands" => $facture->getCommandes(),
			"organization" => $devis->getOrganization(),
			"devis" => $devis,
		]);
	}

	//a faire function pour recup toutes les factures sans devis ID et generation pdf de facture au click du bouton télécharger facture
	#[Route("/organization/{organization}/bills", name: "app_organization_bills", methods: ["GET"])]
	public function organizationBills(Organization $organization)
	{
		if (!$this->isGranted(OrganizationVoter::READ_FACTURE, $organization)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour voir les factures de cette Organisation."
			);
			return $this->redirectToRoute("app_organizations");
		}
		return $this->render("facture/organization.factures.html.twig", [
			"bills" => $organization->getFactures(),
			"organization" => $organization,
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/bill/{facture}/download",
			name: "app_facture_download",
			methods: ["GET"]
		)
	]
	public function download(
		Facture $facture,
		Organization $organization,
		Devis $devis,
		GeneratePdfService $generatePdfService,
		CalculationService $calculationService
	): Response {
		if (
			$organization->getId() !== $facture->getOrganization()->getId() ||
			$devis->getId() !== $facture->getDevis()->getId()
		) {
			$devisFromFacture = $facture->getDevis();
			$this->addFlash("error", "Vous ne pouvez pas télécharger cette facture.");
			return $this->redirectToRoute("app_bills_by_devis", [
				"organization" => $devisFromFacture->getOrganization()->getId(),
				"devis" => $devisFromFacture->getId(),
			]);
		}

		$response = new Response();
		$organization = $devis->getOrganization();

		$totalHt = $calculationService->doCalculationForTotalHT(
			$devis->getCommandes(),
			$devis->getDiscount()
		);

		$kernel_dir = $this->getParameter("kernel.project_dir");

		if ($organization->getLogoName() === null) {
			$imagePath = $kernel_dir . "/public/assets/images/default.jpg";
		} else {
			$imagePath =
				$kernel_dir .
				"/public/storage/images/organizations/" .
				$devis->getOrganization()->getLogoName();
		}

		$html = $this->renderView("generate_pdf/facture-pdf.html.twig", [
			"facture" => $facture,
			"totalHt" => $totalHt,
			"logoPath" => $imagePath,
		]);

		$filenamePdf = $organization->getName() . "-facture-" . $facture->getId() . ".pdf";

		try {
			/** @var Dompdf $pdf */
			$pdf = $generatePdfService->generatePdf($html);
			$response->setContent($pdf->stream($filenamePdf));
			$response->setStatusCode(Response::HTTP_OK);
			$response->headers->set("content-type", "application/pdf");
		} catch (\Exception $e) {
			$response->setContent("");
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->addFlash("error", $e->getMessage());
			return $this->redirectToRoute("app_facture_get_id", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
				"facture" => $facture,
			]);
		}

		return $response;
	}
}
