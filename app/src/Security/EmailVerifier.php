<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Symfony\Component\Mime\Address;

class EmailVerifier
{
	public function __construct(
		private VerifyEmailHelperInterface $verifyEmailHelper,
		private MailerInterface $mailer,
		private EntityManagerInterface $entityManager,
		private Environment $twig
	) {
	}

	public function sendEmailConfirmation(
		string $verifyEmailRouteName,
		UserInterface $user,
		TemplatedEmail $email
	): void {
		$signatureComponents = $this->verifyEmailHelper->generateSignature(
			$verifyEmailRouteName,
			$user->getId(),
			$user->getEmail(),
			["id" => $user->getId()]
		);

		$context = $email->getContext();
		$context["signedUrl"] = $signatureComponents->getSignedUrl();
		$context["expiresAtMessageKey"] = $signatureComponents->getExpirationMessageKey();
		$context["expiresAtMessageData"] = $signatureComponents->getExpirationMessageData();

		$email->context($context);
		$htmlContent = $this->twig->render($email->getHtmlTemplate(), $email->getContext());

		$send = (new Email())
			->from(new Address("billy.esgi@gmail.com", "support-billy@gmail.com"))
			->to($user->getEmail())
			->subject("Confirmer votre email")
			->html($htmlContent);
		(new Mailer(new GmailSmtpTransport("billy.esgi@gmail.com", "qmrp leef onim orrj")))->send($send);

		//$this->mailer->send($email);
	}

	/**
	 * @throws VerifyEmailExceptionInterface
	 */
	public function handleEmailConfirmation(Request $request, UserInterface $user): void
	{
		$this->verifyEmailHelper->validateEmailConfirmation(
			$request->getUri(),
			$user->getId(),
			$user->getEmail()
		);

		$user->setIsVerified(true);

		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}
}
