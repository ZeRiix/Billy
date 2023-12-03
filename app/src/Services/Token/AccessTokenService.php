<?php

namespace App\Services\Token;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\InputBag;

class AccessTokenService extends TokenService
{
	protected static function getPublicKey(): string
	{
		return "file://" . __DIR__ . "/../../../config/jwt/public.pem";
	}

	protected static function getPrivateKey(): string
	{
		return "file://" . __DIR__ . "/../../../config/jwt/private.pem";
	}

	protected static function getPassword(): string
	{
		return "password";
	}

	protected static function getTimeout(): int
	{
		return 3600;
	}

	public static function extractCookie(InputBag $inputBag)
	{
		return self::checkToken($inputBag->get(self::name()));
	}

	public static function createCookie(User $user)
	{
		return new Cookie(
			self::name(),
			self::generateToken($user->getId()),
			time() + self::getTimeout(),
			"/"
		);
	}
}
