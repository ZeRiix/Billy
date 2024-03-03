<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
	private EmailVerifier $emailVerifier;

	public function __construct(EmailVerifier $emailVerifier)
	{
		$this->emailVerifier = $emailVerifier;
	}

	#[Route("/register", name: "app_register")]
	public function register(
		Request $request,
		UserPasswordHasherInterface $userPasswordHasher,
		EntityManagerInterface $entityManager
	): Response {
		if ($this->getUser()) {
			return $this->redirectToRoute("app_organizations");
		}

		/** @var User $user */
		$user = new User();
		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user->setPassword($userPasswordHasher->hashPassword($user, $form->get("password")->getData()));
			$entityManager->persist($user);
			$entityManager->flush();

			// generate a signed url and email it to the user
			$this->emailVerifier->sendEmailConfirmation(
				"app_verify_email",
				$user,
				(new TemplatedEmail())
					->from(new Address("support@billy.com", "billy"))
					->to($user->getEmail())
					->subject("Confimation de votre email")
					->htmlTemplate("registration/confirmation_email.html.twig")
			);
			// do anything else you need here, like send an email

			$this->addFlash("success", "Un email a été envoyé.");
			return $this->redirectToRoute("app_login");
		}

		return $this->render("registration/register.html.twig", [
			"registrationForm" => $form->createView(),
		]);
	}

	#[Route("/verify/email", name: "app_verify_email")]
	public function verifyUserEmail(
		Request $request,
		TranslatorInterface $translator,
		UserRepository $userRepository
	): Response {
		$id = $request->query->get("id");

		if (null === $id) {
			return $this->redirectToRoute("app_home");
		}

		/** @var User $user */
		$user = $userRepository->find($id);

		if (null === $user) {
			return $this->redirectToRoute("app_home");
		}

		// validate email confirmation link, sets User::isVerified=true and persists
		try {
			$this->emailVerifier->handleEmailConfirmation($request, $user);
		} catch (VerifyEmailExceptionInterface $exception) {
			$this->addFlash(
				"verify_email_error",
				$translator->trans($exception->getReason(), [], "VerifyEmailBundle")
			);

			return $this->redirectToRoute("app_register");
		}

		$this->addFlash("success", "Votre email à bien été vérifié.");

		return $this->redirectToRoute("app_login");
	}
}
