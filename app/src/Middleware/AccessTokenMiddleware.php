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
		$token = AccessTokenService::extractCookie($this->request->cookies);

		if ($token) {
			return $this->output("has", $token);
		} else {
			return $this->output("missing");
		}
	}
}
