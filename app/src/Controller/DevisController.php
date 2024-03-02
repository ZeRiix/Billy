<?php

namespace App\Controller;

//local
use Symfony\Component\HttpFoundation\Request;
use App\Security\Voter\DevisVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//services
use App\Services\Devis\DevisService;
//entity
use App\Entity\Devis;
use App\Entity\DeviStatus;
use App\Entity\Organization;
//form
use App\Form\CreateDevisForm;
use App\Form\EditDevisForm;
use App\Form\SignDevisForm;
use App\Repository\DevisRepository;
use App\Security\Voter\OrganizationVoter;
use App\Services\Calculation\CalculationService;
use App\Services\Pdf\GeneratePdfService;

class DevisController extends AbstractController
{
	#[Route("/organization/{organization}/quotations", name: "app_devis", methods: ["GET"])]
	public function index(
		Organization $organization,
		Request $request,
		DevisRepository $devisRepository
	): Response {
		if (!$this->isGranted(OrganizationVoter::VIEW, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour voir les devis de cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}

		$editingCheck = !!$request->query->get("editing");
		$lockCheck = !!$request->query->get("lock");
		$signCheck = !!$request->query->get("sign");
		$completedCheck = !!$request->query->get("completed");
		$canceledCheck = !!$request->query->get("canceled");

		if (!$editingCheck && !$lockCheck && !$signCheck && !$completedCheck) {
			return $this->redirect(
				"/organization/{$organization->getId()}/quotations?editing=on&lock=on&sign=on"
			);
		}

		$status = [];
		if ($editingCheck) {
			$status[] = DeviStatus::EDITING;
		}
		if ($lockCheck) {
			$status[] = DeviStatus::LOCK;
		}
		if ($signCheck) {
			$status[] = DeviStatus::SIGN;
		}
		if ($completedCheck) {
			$status[] = DeviStatus::COMPLETED;
		}
		if ($canceledCheck) {
			$status[] = DeviStatus::CANCELED;
		}

		$quotations = $devisRepository->findBy(
			[
				"organization" => $organization,
				"status" => $status,
			],
			[
				"created_at" => "desc",
			]
		);

		return $this->render("devis/devis.html.twig", [
			"canCreate" => $this->isGranted(DevisVoter::CREATE, $organization),
			"organization" => $organization,
			"quotations" => $quotations,
			"editingCheck" => $editingCheck,
			"lockCheck" => $lockCheck,
			"signCheck" => $signCheck,
			"completedCheck" => $completedCheck,
			"canceledCheck" => $canceledCheck,
		]);
	}

	#[Route("/organization/{organization}/quotation", name: "app_create_devis", methods: ["GET", "POST"])]
	public function create(Request $request, Organization $organization, DevisService $devisService): Response
	{
		if (!$this->isGranted(DevisVoter::CREATE, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour créer un devis.");
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		$devis = new Devis();
		$form = $this->createForm(CreateDevisForm::class, $devis);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$devisService->create($devis, $organization);
				$this->addFlash("success", "Le devis a bien été créé.");
				return $this->redirectToRoute("app_update_devis", [
					"organization" => $organization->getId(),
					"devis" => $devis->getId(),
				]);
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
				return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
			}
		}

		return $this->render("devis/devis.create.html.twig", [
			"form" => $form->createView(),
			"organization" => $organization,
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}",
			name: "app_update_devis",
			methods: ["GET", "POST"]
		)
	]
	public function update(
		Request $request,
		Devis $devis,
		DevisService $devisService,
		CalculationService $calculationService
	): Response {
		$organization = $devis->getOrganization();
		if (!$this->isGranted(DevisVoter::UPDATE, $devis)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour éditer ce devis.");
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		$form = $this->createForm(EditDevisForm::class, $devis, [
			"organization_id" => $organization->getId(),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$devisService->update($devis);
				if ($request->get("redirectCommand") !== "on" && $request->get("redirectSend") !== "on") {
					$this->addFlash("success", "Le devis a bien été édité.");
				}
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
			}
		}

		$commandes = $devis->getCommandes();
		$totalHt = $calculationService->doCalculationForTotalHT($commandes, $devis->getDiscount());

		if ($request->get("redirectCommand") === "on" && $form->isValid()) {
			return $this->redirectToRoute("app_create_commande", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		if ($request->get("redirectSend") === "on" && $form->isValid()) {
			return $this->redirectToRoute("app_send_devis", [
				"organization" => $organization->getId(),
				"devis" => $devis->getId(),
			]);
		}

		return $this->render("devis/devis.update.html.twig", [
			"form" => $form->createView(),
			"quotation" => $devis,
			"organization" => $organization,
			"commandes" => $commandes,
			"totalHT" => $totalHt,
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/generate-pdf",
			name: "app_generate_pdf_devis",
			methods: ["GET"]
		)
	]
	public function generatePdf(
		Devis $devis,
		Organization $organization,
		GeneratePdfService $generatePdfService,
		CalculationService $calculationService
	): Response {
		if (
			$organization->getId() !== $devis->getOrganization()->getId() ||
			($devis->getStatus() !== DeviStatus::LOCK && $devis->getStatus() !== DeviStatus::SIGN)
		) {
			return new Response("Devis inaccessible.", 400);
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

		$imageSign = $devis->getImageSignName();

		$html = $this->renderView("generate_pdf/devis-pdf.html.twig", [
			"devis" => $devis,
			"totalHt" => $totalHt,
			"logoPath" => $imagePath,
			"imageSign" => $imageSign
				? $kernel_dir . "/public/storage/images/devis/sign/" . $imageSign
				: null,
		]);

		$filenamePdf = $organization->getName() . "-devis-" . $devis->getId() . ".pdf";

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
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		return $response;
	}

	#[Route("/organization/{organization}/quotation/{devis}/send", name: "app_send_devis", methods: ["GET"])]
	public function sendDevis(Devis $devis, DevisService $devisService)
	{
		$organization = $devis->getOrganization();
		if (!$this->isGranted(OrganizationVoter::WRITE_DEVIS, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour éditer ce devis.");
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		try {
			$devisService->sendDevis($devis);
			$this->addFlash("success", "Le devis a bien été envoyé.");
		} catch (\Exception $e) {
			$this->addFlash("error", $e->getMessage());
		}

		return $this->redirectToRoute("app_update_devis", [
			"organization" => $organization->getId(),
			"devis" => $devis->getId(),
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/preview",
			name: "app_preview_devis",
			methods: ["GET", "POST"]
		)
	]
	public function previewDevis(
		Devis $devis,
		Organization $organization,
		Request $request,
		DevisRepository $devisRepository,
		CalculationService $calculationService
	) {
		if (
			$organization->getId() !== $devis->getOrganization()->getId() ||
			($devis->getStatus() !== DeviStatus::LOCK && $devis->getStatus() !== DeviStatus::SIGN)
		) {
			return new Response("Devis inaccessible.", 400);
		}

		$form = $this->createForm(SignDevisForm::class, $devis);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $devis->getStatus() === DeviStatus::LOCK) {
			try {
				$devis->setStatus(DeviStatus::SIGN);
				$devisRepository->save($devis);
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
			}
		}

		$totalHt = $calculationService->doCalculationForTotalHT(
			$devis->getCommandes(),
			$devis->getDiscount()
		);
		$logoName = $devis->getOrganization()->getLogoName();
		$imageSign = $devis->getImageSignName();

		return $this->render("devis/devis.preview.html.twig", [
			"form" => $form,
			"devis" => $devis,
			"totalHt" => $totalHt,
			"logoPath" => $logoName
				? "/storage/images/organizations/" . $logoName
				: "/assets/images/default.jpg",
			"imageSign" => $imageSign ? "/storage/images/devis/sign/" . $imageSign : null,
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/unlock",
			name: "app_unlock_devis",
			methods: ["GET"]
		)
	]
	public function unlockDevis(Devis $devis, DevisRepository $devisRepository)
	{
		$organization = $devis->getOrganization();
		if (
			!$this->isGranted(OrganizationVoter::WRITE_DEVIS, $organization) ||
			$devis->getStatus() !== DeviStatus::LOCK
		) {
			$this->addFlash("error", "Vous n'avez pas les droits pour éditer ce devis.");
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		try {
			$devis->setStatus(DeviStatus::EDITING);
			$devisRepository->save($devis);
			$this->addFlash("success", "Le devis a bien étais éditer.");
		} catch (\Exception $e) {
			$this->addFlash("error", $e->getMessage());
		}

		return $this->redirectToRoute("app_update_devis", [
			"organization" => $organization->getId(),
			"devis" => $devis->getId(),
		]);
	}

	#[
		Route(
			"/organization/{organization}/quotation/{devis}/cancel",
			name: "app_cancel_devis",
			methods: ["GET"]
		)
	]
	public function cancelDevis(Devis $devis, DevisRepository $devisRepository)
	{
		$organization = $devis->getOrganization();
		if (
			!$this->isGranted(OrganizationVoter::WRITE_DEVIS, $organization) ||
			$devis->getStatus() !== DeviStatus::EDITING
		) {
			$this->addFlash("error", "Vous n'avez pas les droits pour éditer ce devis.");
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		try {
			$devis->setStatus(DeviStatus::CANCELED);
			$devisRepository->save($devis);
			$this->addFlash("success", "Le devis a bien étais éditer.");
		} catch (\Exception $e) {
			$this->addFlash("error", $e->getMessage());
		}

		return $this->redirectToRoute("app_update_devis", [
			"organization" => $organization->getId(),
			"devis" => $devis->getId(),
		]);
	}
}
