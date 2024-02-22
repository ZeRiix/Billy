<?php

namespace App\Services;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailService
{
	public static function send(
		string $to,
		string $subject,
		string $body,
		bool $isContact,
		$filePath = false
	): void {
		$email = self::create(
			$to,
			$subject,
			$body,
			$isContact ? "MAILER_CONTACT_FROM" : "MAILER_NOREPLY_FROM"
		);
		if ($filePath) {
			$email->attachFromPath($filePath);
		}
		(new Mailer(new GmailSmtpTransport("billy.esgi@gmail.com", "qmrp leef onim orrj")))->send($email);
	}

	private static function create(string $to, string $subject, string $body, string $from)
	{
		return (new Email())
			->from(new Address("billy.esgi@gmail.com", $_ENV[$from]))
			->to($to)
			->subject($subject)
			->html($body);
	}

	public static function createHtmlBodyWithTwig(string $template, array $context): string
	{
		return (new \Twig\Environment(
			new \Twig\Loader\FilesystemLoader(__DIR__ . "/../../templates")
		))->render($template, $context);
	}
}
