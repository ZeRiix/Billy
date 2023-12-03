<?php

namespace App\Middleware;

use App\Repository\UserRepository;

class SelfUserMiddleware extends AbstractMiddleware
{
	private UserRepository $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		new Middleware(
			AccessTokenMiddleware::class,
			"has",
			output: "userId",
			redirectTo: "/login"
		);

		$user = $this->userRepository->getById(Middleware::$floor["userId"]);
		if ($user) {
			return $this->output("exist", $user);
		} else {
			return $this->output("notfound");
		}
	}
}
