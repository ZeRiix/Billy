<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
	#[Route(path: "/login", name: "app_login")]
	public function login(AuthenticationUtils $authenticationUtils): Response
	{
		if ($this->getUser()) {
			return $this->redirectToRoute("app_organizations");
		}

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		if ($error) {
			$this->addFlash("error", "Mot de passe ou email incorrect.");
		}

		return $this->render("security/login.html.twig", [
			"last_username" => $lastUsername,
			"error" => $error,
		]);
	}

	#[Route(path: "/logout", name: "app_logout")]
	public function logout(): void
	{
		$this->addFlash("success", "Vous avez été déconnecté avec succès.");
		throw new \LogicException(
			"This method can be blank - it will be intercepted by the logout key on your firewall."
		);
	}
}
