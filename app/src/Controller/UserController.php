<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// local imports
use App\Services\SchemaService;
use App\Services\User\AuthService;
use App\Services\User\UserService;

class UserController extends AbstractController
{
	#[Route("/register", name: "register_user", methods: "POST")]
	public function register(
		Request $request,
		AuthService $authService
	): JsonResponse {
		try {
			// Validate input data
			$data = SchemaService::validateSchema(
				json_decode($request->getContent(), true),
				"register"
			);
			// Create user
			$user = $authService->register($data);

			return new JsonResponse($user, Response::HTTP_CREATED);
		} catch (\Exception $e) {
			return new JsonResponse($e->getMessage(), $e->getCode());
		}
	}

	#[Route("/login", name: "login_user", methods: "POST")]
	public function login(Request $request, AuthService $authService): Response
	{
		try {
			// Validate input data
			$data = SchemaService::validateSchema(
				json_decode($request->getContent(), true),
				"login"
			);

			// get user if exists
			$user = $authService->login($data);
			// create token
			$token = $authService->createToken($user);
			// set token cookie
			$authService->setTokenCookie($token);

			return new Response(null, Response::HTTP_ACCEPTED);
		} catch (\Exception $e) {
			return new JsonResponse($e->getMessage());
		}
	}

	#[Route("/api/users", name: "all_user", methods: "GET")]
	public function getUsers(UserService $userService): JsonResponse
	{
		try {
			$users = $userService->getAll();

			return new JsonResponse($users, Response::HTTP_OK);
		} catch (\Exception $e) {
			return new JsonResponse(
				$e->getMessage(),
				Response::HTTP_INTERNAL_SERVER_ERROR
			);
		}
	}

	#[Route("/api/user/{id}", name: "get_user", methods: "GET")]
	public function getUserById(
		UserService $userService,
		string $id
	): JsonResponse {
		try {
			$user = $userService->getById($id);

			return new JsonResponse($user, Response::HTTP_OK);
		} catch (\Exception $e) {
			return new JsonResponse(
				$e->getMessage(),
				$e->getCode() === 0
					? Response::HTTP_INTERNAL_SERVER_ERROR
					: $e->getCode()
			);
		}
	}

	#[Route("/api/user/{id}", name: "update_user", methods: "DELETE")]
	public function deleteUser(UserService $userService, string $id): Response
	{
		try {
			$userService->deleteById($id);

			return new Response(null, Response::HTTP_NO_CONTENT);
		} catch (\Exception $e) {
			return new JsonResponse($e->getMessage(), $e->getCode());
		}
	}
}