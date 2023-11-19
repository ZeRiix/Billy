<?php

namespace App\Services\User;

use Symfony\Component\HttpFoundation\Response;
// local imports
use App\Repository\UserRepository;
use App\Entity\User;
use Namshi\JOSE\JWS;
use Namshi\JOSE\SimpleJWS;

class AuthService
{
	private UserRepository $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param array[firstName: string, name: string, email: string, password: string] $data
	 * @return User
	 */
	public function register(array $data): array
	{
		// check if user
		if ($this->userRepository->getByEmail($data["email"])) {
			throw new \Exception(
				"Account with email already exists",
				Response::HTTP_CONFLICT
			);
		}
		// create new user
		$user = $this->userRepository->create($data);

		return [
			// horrible
			"id" => $user->getId(),
			"firstname" => $user->getFirstName(),
			"name" => $user->getName(),
			"email" => $user->getEmail(),
			"password" => $user->getPassword(),
			"created_at" => $user->getCreatedAt(),
			"updated_at" => $user->getUpdatedAt(),
		]; // resoudre bug get entity
	}

	/**
	 * @param array[email: string, password: string] $data
	 * @return User
	 */
	public function login(array $data): User
	{
		// check if user
		$user = $this->userRepository->getByEmail($data["email"]);
		if (!$user) {
			throw new \Exception(
				"Account with email not found",
				Response::HTTP_NOT_FOUND
			);
		}
		// check password
		if (!password_verify($data["password"], $user->getPassword())) {
			throw new \Exception("Wrong password", Response::HTTP_UNAUTHORIZED);
		}

		return $user;
	}

	/**
	 * @param string $token
	 * @return void
	 */
	public function setTokenCookie(string $token): void
	{
		// horrible
		// set cookie
		setcookie(
			"jwt-token-billy-app",
			$token,
			time() + 3600,
			"/",
			"localhost",
			true,
			true
		);
		// TODO edit config .env
	}

	/**
	 * @param User $user
	 * @return string
	 */
	public function createToken(User $user): string
	{
		// horrible
		$payload = [
			// define value for token in .env
			"iss" => "localhost",
			"sub" => $user->getId(),
			"iat" => time(),
			"exp" => time() + 3600,
		];

		$header = [
			// define value for token in .env
			"alg" => "HS256",
			"typ" => "JWS",
		];

		return $this->generateToken($payload, $header);
	}

	public function checkToken(string $token): bool
	{
		// horrible
		$jws = SimpleJWS::load($token);
		$jws->verify(
			openssl_pkey_get_public(
				"file://" . __DIR__ . "/../../../config/jwt/public.pem"
			), // define path in .env
			"password" // define password in .env
		);
		$payload = $jws->getPayload();
		if (isset($payload["exp"])) {
			$now = new \DateTime("now");

			if ($payload["exp"]) {
				if ($now->getTimestamp() - $payload["exp"] > 0) {
					throw new \Exception(
						"Token expired",
						Response::HTTP_UNAUTHORIZED
					);
				}
			}
		}

		return true;
	}

	/**
	 * @param array $payload
	 * @param array $header
	 * @return string
	 */
	public function generateToken(array $payload, array $header): string
	{
		$jws = new JWS($header, "OpenSSL");
		$jws->setPayload($payload);
		$jws->sign(
			openssl_pkey_get_private(
				"file://" . __DIR__ . "/../../../config/jwt/private.pem"
			), // define path in .env
			"password" // define password in .env
		);
		$token = $jws->getTokenString();

		return $token;
	}
}
