<?php

namespace App\Controller;

//local

use App\Entity\BillReminder;
use App\Entity\Devis;
use App\Entity\DeviStatus;
use App\Entity\Facture;
use App\Entity\FactureStatus;
use App\Entity\Organization;
use App\Form\CreateBillReminderForm;
use App\Repository\FactureRepository;
use App\Security\Voter\OrganizationVoter;
use App\Services\BillReminder\BillReminderService;
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
		$organization = $devis->getOrganization();

		if (
			!$this->isGranted(OrganizationVoter::READ_FACTURE, $organization) ||
			($devis->getStatus() != DeviStatus::SIGN && $devis->getStatus() != DeviStatus::COMPLETED)
		) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour voir les factures de ce devis ou le devis n'est pas signé."
			);
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}
		$canCreate = !empty($devisService->getCommandesNotFactured($devis));
		return $this->render("facture/factures.html.twig", [
			"bills" => $devis->getFactures(),
			"organization" => $organization,
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
		$organization = $devis->getOrganization();
		if (
			!$this->isGranted(OrganizationVoter::WRITE_FACTURE, $organization) ||
			$devis->getStatus() != DeviStatus::SIGN
		) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour créer une facture pour cette Organisation ou le devis n'est pas signé."
			);
			return $this->redirectToRoute("app_update_devis", [
				"organization" => $organization->getId(),
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
					$organization,
					$devis->getClient(),
					$devis,
					$commandeIds
				);

				$this->addFlash("success", "La facture à bien été créee.");
				return $this->redirectToRoute("app_bills_by_devis", [
					"organization" => $organization->getId(),
					"devis" => $devis->getId(),
				]);
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"facture/facture.create.html.twig",
			[
				"commandes" => $devisService->getCommandesNotFactured($devis),
				"organization" => $devis->getOrganization(),
				"devis" => $devis,
			],
			$response
		);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/bill/{facture}/remind",
			name: "app_create_remind_facture",
			methods: ["GET", "POST"]
		)
	]
	public function createRemindBill(
		Request $request,
		Facture $facture,
		BillReminderService $billReminderService
	): Response {
		$organization = $facture->getOrganization();
		if (
			!$this->isGranted(OrganizationVoter::WRITE_FACTURE, $organization) ||
			$facture->getStatut() != FactureStatus::WAITING
		) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour créer une facture pour cette Organisation ou la facture est déjà payée."
			);
			return $this->redirectToRoute("app_facture_get_id", [
				"organization" => $organization->getId(),
				"devis" => $facture->getDevis()->getId(),
				"facture" => $facture->getId(),
			]);
		}

		$billReminder = new BillReminder();
		$form = $this->createForm(CreateBillReminderForm::class, $billReminder);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$billReminder = $form->getData();

			try {
				$billReminderService->create($billReminder, $facture);
				$this->addFlash("success", "Le rappel de facture a bien été créé.");
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render("facture/facture.remind.create.html.twig", [
			"form" => $form->createView(),
			"facture" => $facture,
		]);
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
	public function organizationBills(
		Organization $organization,
		FactureRepository $factureRepository,
		Request $request
	) {
		if (!$this->isGranted(OrganizationVoter::READ_FACTURE, $organization)) {
			$this->addFlash(
				"error",
				"Vous n'avez pas les droits pour voir les factures de cette Organisation."
			);
			return $this->redirectToRoute("app_organizations");
		}

		$payeCheck = !!$request->query->get("payé");
		$waitingCheck = !!$request->query->get("waiting");

		if (!$payeCheck && !$waitingCheck) {
			return $this->redirect("/organization/{$organization->getId()}/bills?waiting=on");
		}

		$status = [];
		if ($payeCheck) {
			$status[] = FactureStatus::PAID;
		}
		if ($waitingCheck) {
			$status[] = FactureStatus::WAITING;
		}

		$bills = $factureRepository->findBy(
			[
				"organization" => $organization,
				"statut" => $status,
			],
			[
				"created_at" => "desc",
			]
		);

		return $this->render("facture/organization.factures.html.twig", [
			"bills" => $bills,
			"organization" => $organization,
			"payeCheck" => $payeCheck,
			"waitingCheck" => $waitingCheck,
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/bill/{facture}/preview",
			name: "app_preview_bill",
			methods: ["GET"]
		)
	]
	public function previewBill(
		Facture $facture,
		Devis $devis,
		Organization $organization,
		CalculationService $calculationService
	) {
		if (
			$organization->getId() !== $facture->getOrganization()->getId() ||
			$devis->getId() !== $facture->getDevis()->getId()
		) {
			$devisFromFacture = $facture->getDevis();
			$this->addFlash("error", "Vous ne pouvez pas visualiser cette facture.");
			return $this->redirectToRoute("app_bills_by_devis", [
				"organization" => $devisFromFacture->getOrganization()->getId(),
				"devis" => $devisFromFacture->getId(),
			]);
		}

		$totalHt = $calculationService->doCalculationForTotalHT(
			$devis->getCommandes(),
			$devis->getDiscount()
		);
		$logoName = $organization->getLogoName();

		return $this->render("facture/facture.preview.html.twig", [
			"facture" => $facture,
			"totalHt" => $totalHt,
			"logoPath" => $logoName
				? "/storage/images/organizations/" . $logoName
				: "/assets/images/default.jpg",
			"devis" => $facture->getDevis(),
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
			$imagePath = $kernel_dir . "/public/storage/images/organizations/" . $organization->getLogoName();
		}

		$html = $this->renderView("generate_pdf/facture-pdf.html.twig", [
			"facture" => $facture,
			"totalHt" => $totalHt,
			"logoPath" => $imagePath,
		]);

		$filenamePdf = $organization->getName() . "-facture-" . $facture->getChrono() . ".pdf";

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

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/bill/{facture}/pay",
			name: "app_pay_facture",
			methods: ["GET"]
		)
	]
	public function payBill(
		Facture $facture,
		FactureRepository $factureRepository,
		BillReminderService $billReminderService
	) {
		$devis = $facture->getDevis();
		if ($devis->getStatus() != DeviStatus::SIGN && $devis->getStatus() != DeviStatus::COMPLETED) {
			$this->addFlash(
				"error",
				"Vous ne pouvez pas changer le statut d'une facture si le devis n'est pas signé ou complété."
			);
			return $this->redirectToRoute("app_facture_get_id", [
				"organization" => $devis->getOrganization()->getId(),
				"devis" => $devis->getId(),
				"facture" => $facture->getId(),
			]);
		}

		try {
			if ($facture->getStatut() == FactureStatus::WAITING) {
				$facture->setStatut(FactureStatus::PAID);
				$factureRepository->save($facture);
				$billReminderService->deleteAllRemindersForFacture($facture);
				$this->addFlash("success", "La facture est maintenant payée.");
			} else {
				$this->addFlash("error", "La facture est déjà payée.");
			}
		} catch (\Exception $e) {
			$this->addFlash("error", $e->getMessage());
		}

		return $this->redirectToRoute("app_facture_get_id", [
			"organization" => $devis->getOrganization()->getId(),
			"devis" => $devis->getId(),
			"facture" => $facture->getId(),
		]);
	}
}
