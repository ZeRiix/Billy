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
use App\Security\Voter\OrganizationVoter;
use App\Services\Pdf\GeneratePdfService;

class DevisController extends AbstractController
{

	#[Route('/organization/{organization}/quotations', name: 'app_devis', methods: ["GET"])]
	public function index(Organization $organization): Response
	{
		if (!$this->isGranted(OrganizationVoter::VIEW, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour voir les devis de cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		return $this->render('devis/devis.html.twig', [
			'canCreate' => $this->isGranted(DevisVoter::CREATE, $organization),
			'organization' => $organization,
			'quotations' => $organization->getDevis(),
		]);
	}

	#[Route('/organization/{organization}/quotation', name: "app_create_devis", methods: ["GET", "POST"])]
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
				return $this->redirectToRoute("app_update_devis", ["organization" => $organization->getId(), "devis" => $devis->getId()]);
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
				return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
			}
		}

		return $this->render('devis/devis.create.html.twig', [
			"form" => $form->createView()
		]);
	}

	#[Route('/organization/{organization}/quotation/{devis}', name: "app_update_devis", methods: ["GET", "POST"])]
	public function update(Request $request, Devis $devis, DevisService $devisService): Response
	{
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
				$this->addFlash("success", "Le devis a bien été édité.");
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
			}
		}	

		$commandes = $devis->getCommandes();
		$totalHt = $this->doCalculationForTotalHT($commandes, $devis->getDiscount());

		if($request->get("redirectCommand") === "on" && $form->isValid()){
			return $this->redirectToRoute('app_create_commande', ["organization" => $organization->getId(), "devis" => $devis->getId()]);
		}

		return $this->render('devis/devis.update.html.twig', [
			"form" => $form->createView(),
			"quotation" => $devis,
			"organization" => $organization,
			"commandes" => $commandes,
			"totalHT" => $totalHt
		]);
	}

	private function doCalculationForTotalHT($commandes, $discount) 
	{
		$totalHt = 0;
		foreach ($commandes as $commande) {
			$totalHt += $commande->getQuantity() * $commande->getUnitPrice();
		}

		if ($discount > 0) {
			$totalHt = $totalHt * (1 - ($discount / 100));
		}

		return $totalHt;
	}

	#[Route('/organization/{organization}/quotation/{devis}/generate-pdf', name: "app_generate_pdf_devis", methods: ["GET"])]
	public function generatePdf(Devis $devis, Organization $organization, GeneratePdfService $generatePdfService, DevisService $devisService) : Response
	{
		if(!$devisService->findById($devis->getId()) && !$devisService->devisBelongsToOrganization($devis, $organization))
		{
			$this->addFlash("error", "Le devis n'existe pas ou n'appartient pas à cette organisation.");
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		$response = new Response();

		$haveLogo = false;

		$totalHt = $this->doCalculationForTotalHT($devis->getCommandes(), $devis->getDiscount());

		$imagePath = $this->getParameter('kernel.project_dir') . '/public/storage/images/organizations/' . $devis->getOrganization()->getLogoName();

		if (file_exists($imagePath)) {
			$haveLogo = true;
		}

		$html = $this->renderView('generate_pdf/devis-pdf.html.twig', [
			"devis" => $devis,
			"totalHt" => $totalHt,
			"logoPath" => $imagePath,
			"haveLogo" => $haveLogo
		]);

		$filenamePdf = "devis-" . $devis->getId() . ".pdf";

		try {
			/** @var Dompdf $pdf */
			$pdf = $generatePdfService->generatePdf($html);
			$response->setContent($pdf->stream($filenamePdf));
			$response->setStatusCode(Response::HTTP_OK);
		} catch (\Exception $e) {
			$response->setContent("");
			$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			$this->addFlash("error", $e->getMessage());
			return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
		}

		return $response;
	}
}