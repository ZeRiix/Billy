<?php

namespace App\Middleware;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

// local imports
use App\Services\User\AuthService;

class CheckTokenMiddleware 
{
	private AuthService $authService;

	public function __construct(AuthService $authService)
	{
		$this->authService = $authService;
	}

	public function onKernelRequest(RequestEvent $request)
	{
		try {
			if (!$this->requiresJwtValidation($request->getRequest())) {
				return;
			}
			// check if token exists
			$token = $request->getRequest()->cookies->get('jwt-token-billy-app');

			if (!$token || $token === 'null') {
				throw new \Exception('No token provided', Response::HTTP_UNAUTHORIZED);
			}

			$this->authService->checkToken($token);
		} catch (\Exception $e) {
			throw new UnauthorizedHttpException('Cookie', $e->getMessage(), $e, $e->getCode());
		}
	}

	private function requiresJwtValidation(Request $request): bool
	{
		$pathInfo = $request->getPathInfo();
		$allowedPrefixes = ['/api', '/test'];  // Add other prefix or path is here
		

		foreach ($allowedPrefixes as $prefix) {
			if (str_starts_with($pathInfo, $prefix)) {
				return true;
			}
		}

		return false;
	}
}