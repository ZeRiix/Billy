<?php

namespace App\Services;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailService
{
	public static function send(string $to, string $subject, string $body, bool $isContact): void
	{
		$email = self::create(
			$to,
			$subject,
			$body,
			$isContact ? "MAILER_CONTACT_FROM" : "MAILER_NOREPLY_FROM"
		);
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
}
