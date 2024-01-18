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
use App\Entity\Organization;
//form
use App\Form\CreateDevisForm;
use App\Form\EditDevisForm;

class DevisController extends AbstractController
{

	#[Route('/organization/{organization}/devis', name: 'app_devis', methods: ["GET"])]
	public function index(Organization $organization): Response
	{
		if (!$this->isGranted(DevisVoter::VIEW, $organization)) {
			$this->addFlash("error", "Vous n'avez pas les droits pour voir les devis de cette organisation.");
			return $this->redirectToRoute("app_organizations");
		}
		return $this->render('devis/devis.html.twig', [
			'canCreate' => $this->isGranted(DevisVoter::CREATE, $organization),
			'organization' => $organization
		]);
	}

	#[Route('/organization/{organization}/devis/new', name: "app_create_devis", methods: ["GET", "POST"])]
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

	#[Route('/organization/{organization}/devis/{devis}', name: "app_update_devis", methods: ["GET", "POST"])]
	public function update(Request $request, Organization $organization, Devis $devis, DevisService $devisService): Response
	{
		if (!$this->isGranted(DevisVoter::UPDATE, $organization)) {
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
				return $this->redirectToRoute("app_update_devis", ["devis" => $devis->getId()]);
			} catch (\Exception $e) {
				$this->addFlash("error", $e->getMessage());
				return $this->redirectToRoute("app_devis", ["organization" => $organization->getId()]);
			}
		}

		return $this->render('devis/devis.update.html.twig', [
			"form" => $form->createView(),
			"devis" => $devis,
			"organization" => $organization
		]);
	}
}