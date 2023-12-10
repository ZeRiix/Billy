<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractMiddleware
{
	abstract public function handler(mixed $input, ?array $options): mixed;

	public function output(string $info, mixed $data = null)
	{
		$this->info = $info;
		$this->data = $data;
	}

	private ?string $info = null;

	public function getInfo()
	{
		return $this->info;
	}

	private mixed $data = null;

	public function getData()
	{
		return $this->data;
	}

	public function redirectTo(string $redirectTo)
	{
		throw new HttpException(Response::HTTP_FOUND, headers: ["Location" => $redirectTo]);
	}
}
