<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Form\CreateOrganizationFormType;
use App\Services\Organization\OrganizationService;
use App\Services\Token\AccessTokenService;
use App\Services\User\UserService;
use App\Middleware\AccessTokenMiddleware;
use App\Middleware\Middleware;
use App\Repository\RoleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends MiddlewareController
{
	#[Route("/organization", name: "app_organization", methods: ["GET", "POST"])]
	#[Middleware(AccessTokenMiddleware::class, "has", redirectTo: "/login")]
	public function create(
		Request $request,
		UserService $userService,
		OrganizationService $organizationService
	): Response {
		$response = new Response();
		$organization = new Organization();
		$form = $this->createForm(CreateOrganizationFormType::class, $organization);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			/** @var Organization */
			$organization = $form->getData();
			$payloadCookie = AccessTokenService::extractCookie($request->cookies);
			$userId = $payloadCookie;
			/** @var User */
			$user = $userService->getById($userId);
			$organization->setCreatedBy($user);

			try {
				$organizationService->createOrganization($organization, $user);
				$response->setStatusCode(Response::HTTP_OK);
				$this->addFlash("success", "L'organisation à bien été créée.");
			} catch (\Exception $e) {
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
				$this->addFlash("error", $e->getMessage());
			}
		}

		return $this->render(
			"organization/index.html.twig",
			[
				"form" => $form->createView(),
			],
			$response
		);
	}
}
