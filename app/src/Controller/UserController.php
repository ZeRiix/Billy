<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserRegister;
use App\Form\ChangePasswordForm;
use App\Form\ForgetPasswordForm;
use App\Form\UserLoginForm;
use App\Form\UserRegisterForm;
use App\Middleware\AccessTokenMiddleware;
use App\Middleware\Middleware;
use App\Repository\ForgetPasswordRepository;
use App\Services\Token\AccessTokenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\User\AuthService;

class UserController extends MiddlewareController
{
	#[Route("/register", name: "register_user", methods: ["GET", "POST"])]
	#[Middleware(AccessTokenMiddleware::class, "missing", redirectTo: "/dashboard")]
	public function register(Request $request, AuthService $authService)
	{
		$response = new Response();

		// create form
		$userRegister = new UserRegister();
		$userRegisterForm = $this->createForm(UserRegisterForm::class, $userRegister);
		$userRegisterForm->handleRequest($request);

		if ($userRegisterForm->isSubmitted() && $userRegisterForm->isValid()) {
			$userRegister = $userRegisterForm->getData();

			try {
				$authService->register($userRegister);
				$this->addFlash("success", "Email de confirmation envoyé.");
				$response->setStatusCode(Response::HTTP_CREATED);
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			}
		}

		return $this->render(
			"user/register.html.twig",
			[
				"userRegisterForm" => $userRegisterForm->createView(),
			],
			$response
		);
	}

	#[Route("/validate", name: "validate_user", methods: "GET")]
	#[Middleware(AccessTokenMiddleware::class, "missing", redirectTo: "/dashboard")]
	public function validate(Request $request, AuthService $authService)
	{
		$response = new Response();
		$response->setStatusCode(Response::HTTP_FOUND);

		try {
			$user = $authService->validate($request->query->get("id"));

			// give accessToken to user
			$response->headers->setCookie(AccessTokenService::createCookie($user));

			// redirect user to user dashboard
			$response->headers->set("Location", "/dashboard");
		} catch (\Exception $error) {
			$response->headers->set("Location", "/");
		}

		return $response;
	}

	#[Route("/login", name: "login_user", methods: ["GET", "POST"])]
	#[Middleware(AccessTokenMiddleware::class, "missing", redirectTo: "/dashboard")]
	public function login(Request $request, AuthService $authService)
	{
		$response = new Response();

		// create user form
		$user = new User();
		$userLoginForm = $this->createForm(UserLoginForm::class, $user);
		$userLoginForm->handleRequest($request);

		if ($userLoginForm->isSubmitted() && $userLoginForm->isValid()) {
			$user = $userLoginForm->getData();

			try {
				$user = $authService->login($user);

				// give accessToken to user
				$response->headers->setCookie(AccessTokenService::createCookie($user));

				// redirect user to user dashboard
				$response->setStatusCode(Response::HTTP_FOUND);
				$response->headers->set("Location", "/dashboard");
				return $response;
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			}
		}

		return $this->render(
			"user/login.html.twig",
			[
				"userLoginForm" => $userLoginForm->createView(),
			],
			$response
		);
	}

	#[Route("/forget-password", name: "forget-password", methods: ["GET", "POST"])]
	#[Middleware(AccessTokenMiddleware::class, "missing", redirectTo: "/dashboard")]
	public function forgetPassword(Request $request, AuthService $authService)
	{
		$response = new Response();

		$user = new User();
		$forgetPasswordForm = $this->createForm(ForgetPasswordForm::class, $user);
		$forgetPasswordForm->handleRequest($request);

		if ($forgetPasswordForm->isSubmitted() && $forgetPasswordForm->isValid()) {
			$user = $forgetPasswordForm->getData();

			try {
				$authService->forgetPassword($user);

				// redirect user to user dashboard
				$this->addFlash("success", "Email de confirmation envoyé.");
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			}
		}

		return $this->render(
			"user/forgetPassword.html.twig",
			[
				"forgetPasswordForm" => $forgetPasswordForm->createView(),
			],
			$response
		);
	}

	#[Route("/change-password", name: "change-password", methods: ["GET", "POST"])]
	public function changePassword(
		Request $request,
		AuthService $authService,
		ForgetPasswordRepository $forgetPasswordRepository
	) {
		$response = new Response();

		// check if can change password
		$forgetPassword = $request->query->get("id")
			? $forgetPasswordRepository->getById($request->query->get("id"))
			: null;

		if (!$forgetPassword) {
			$response->setStatusCode(Response::HTTP_FOUND);
			$response->headers->set("Location", "/");
			return $response;
		}

		$user = new User();
		$changePasswordForm = $this->createForm(ChangePasswordForm::class, $user);
		$changePasswordForm->handleRequest($request);

		if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
			$user = $changePasswordForm->getData();

			try {
				$authService->changePassword($user, $forgetPassword);

				// redirect user to user dashboard
				$response->setStatusCode(Response::HTTP_FOUND);
				$response->headers->set("Location", "/login");
				return $response;
			} catch (\Exception $error) {
				$this->addFlash("error", $error->getMessage());
				$response->setStatusCode(Response::HTTP_BAD_REQUEST);
			}
		}

		return $this->render(
			"user/changePassword.html.twig",
			[
				"changePasswordForm" => $changePasswordForm->createView(),
			],
			$response
		);
	}
}
