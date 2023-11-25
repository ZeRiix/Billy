<?php

namespace App\Middleware;

use Attribute;
use DI;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[Attribute(Attribute::TARGET_METHOD)]
class Middleware
{
	private static DI\Container $container;

	public function __construct(
		string $className,
		string $info,
		?string $input = null,
		?string $output = null,
		?array $options = null,
		?Response $response = null,
		?string $redirectTo = null
	) {
		if (!self::$status) {
			return;
		}

		if (!isset(self::$container)) {
			throw new Exception(
				"Controller is not instance of MiddlewareController."
			);
		}

		$instance = self::$container->get($className);
		if (!($instance instanceof AbstractMiddleware)) {
			throw new Exception("Class is not instance of AbstractMiddleware.");
		}

		$instance->handler(self::$floor[$input] ?? null, $options);

		self::$lastMiddleware = $className;
		self::$lastInfo = $instance->getInfo();
		self::$lastResponse = $response;

		if (self::$lastInfo !== $info) {
			self::$status = false;
			if ($redirectTo) {
				throw new HttpException(
					Response::HTTP_FOUND,
					headers: ["Location" => $redirectTo]
				);
			}
		}

		if ($output) {
			self::$floor[$output] = $instance->getData();
		}
	}

	public static array $floor = [];

	private static ?string $lastMiddleware = null;

	private static ?string $lastInfo = null;

	private static ?Response $lastResponse = null;

	private static bool $status = true;

	public static function getLastMiddleware()
	{
		return self::$lastMiddleware;
	}

	public static function getLastInfo()
	{
		return self::$lastInfo;
	}

	public static function getLastResponse()
	{
		return self::$lastResponse;
	}

	public static function getStatus()
	{
		return self::$status;
	}

	public static function init(
		Request $request,
		ManagerRegistry $managerRegistry
	) {
		self::$container = new DI\Container();
		self::$container->set(Request::class, $request);
		self::$container->set(ManagerRegistry::class, $managerRegistry);
	}
}
