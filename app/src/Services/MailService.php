<?php

namespace App\Services;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class MailService
{
	public static function send(string $to, string $subject, string $body)
	{
		$email = self::create($to, $subject, $body);
		$transprt = new GmailSmtpTransport(
			$_ENV["MAILER_USERNAME"],
			$_ENV["MAILER_PASSWORD"]
		);
		$mailer = new Mailer($transprt);
		$mailer->send($email);
	}

	private static function create(string $to, string $subject, string $body)
	{
		return (new Email())
			->from($_ENV["MAILER_USERNAME"])
			->to($to)
			->subject($subject)
			->html($body);
	}
}
