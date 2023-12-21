<?php

namespace App\Middleware;

use App\Repository\UserRepository;
use App\Services\Token\AccessTokenService;
use Symfony\Component\HttpFoundation\Request;

class AccessTokenMiddleware extends AbstractMiddleware
{
	private Request $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		$contentToken = AccessTokenService::extractCookie(
			$this->request->cookies
		);

		if ($contentToken) {
			return $this->output("has", $contentToken);
		} else {
			return $this->output("missing");
		}
	}
}
