<?php

namespace App\Services\Token;

use Namshi\JOSE\JWS;
use Namshi\JOSE\SimpleJWS;

abstract class TokenService
{
	abstract protected static function getPublicKey(): string;
	abstract protected static function getPrivateKey(): string;
	abstract protected static function getPassword(): string;
	abstract protected static function getTimeout(): int;

	protected static function name(): string
	{
		$name = explode("\\", static::class);
		$name = array_pop($name);
		$name = strtolower(preg_replace(["/([a-z\d])([A-Z])/", "/([^_])([A-Z][a-z])/"], "$1_$2", $name));

		return $name;
	}

	private static function createPayload(mixed $data): array
	{
		return [
			"name" => self::name(),
			"data" => $data,
			"iat" => time(),
			"exp" => time() + static::getTimeout(),
		];
	}

	private static function createHeader(): array
	{
		return [
			"alg" => "HS256",
			"typ" => "JWS",
		];
	}

	public static function generateToken(mixed $data): string
	{
		$jws = new JWS(self::createHeader(), "OpenSSL");
		$jws->setPayload(self::createPayload($data));
		$jws->sign(openssl_pkey_get_private(static::getPrivateKey()), static::getPassword());
		$token = $jws->getTokenString();

		return $token;
	}

	public static function checkToken(?string $token): mixed
	{
		if (!isset($token)) {
			return null;
		}

		// horrible
		$jws = SimpleJWS::load($token);
		$jws->verify(openssl_pkey_get_public(static::getPublicKey()), static::getPassword());

		$payload = $jws->getPayload();

		if (!isset($payload["name"]) || $payload["name"] !== self::name()) {
			return null;
		}

		if (!isset($payload["exp"])) {
			return null;
		}

		$now = new \DateTime("now");
		if ($payload["exp"] && $now->getTimestamp() - $payload["exp"] > 0) {
			return false;
		}

		return $payload["data"];
	}
}
