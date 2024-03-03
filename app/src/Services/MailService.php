<?php

namespace App\Services;

use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Vich\UploaderBundle\Twig\Extension\UploaderExtension;
use App\Entity\Organization;

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
		$twig = new Environment(new FilesystemLoader(__DIR__ . "/../../templates"));
		$twig->addExtension(
			new AssetExtension(
				new Packages(
					new \Symfony\Component\Asset\Package(
						new \Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy()
					)
				)
			)
		);
		$twig->addExtension(
			new RoutingExtension(new UrlGenerator(new RouteCollection(), new RequestContext()))
		);
		$twig->addFunction(
			new \Twig\TwigFunction("absolute_url", function (string $path) {
				return $_ENV["APP_URL"] . "/" . $path;
			})
		);
		$twig->addFunction(
			new \Twig\TwigFunction("get_logo_org", function (Organization $organization) {
				return "storage/images/organizations/" . $organization->getLogoName();
			})
		);

		return $twig->render($template, $context);
	}
}
