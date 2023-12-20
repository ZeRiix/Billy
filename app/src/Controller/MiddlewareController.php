<?php

namespace App\Controller;

use App\Middleware\Middleware;
use App\Services\Role\RoleService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\TwigFunction;

abstract class MiddlewareController extends AbstractController
{
	private RoleService $roleService;
	private Request $request;

	public function __construct(
		RequestStack $requestStack,
		ManagerRegistry $managerRegistry,
		RoleService $roleService
	) {
		$this->request = $requestStack->getMainRequest();
		Middleware::init($this->request, $managerRegistry);
		$this->roleService = $roleService;
	}

	protected function renderView(string $view, array $parameters = []): string
	{
		if ($this->request->query->get("error")) {
			$this->addFlash("error", $this->request->query->get("error"));
		}
		if (!$this->container->has("twig")) {
			throw new \LogicException(
				'You cannot use the "renderView" method if the Twig Bundle is not available. Try running "composer require symfony/twig-bundle".'
			);
		}

		/** @var \Twig\Environment $twig */
		$twig = $this->container->get("twig");
		$twig->addGlobal("floor", Middleware::$floor);
		$twig->addFunction(
			new TwigFunction("has_permission", function (string $permission) {
				return $this->roleService->checkPermission(
					Middleware::$floor["user"],
					Middleware::$floor["organization"],
					$permission
				);
			})
		);

		foreach ($parameters as $k => $v) {
			if ($v instanceof FormInterface) {
				$parameters[$k] = $v->createView();
			}
		}

		return $this->container->get("twig")->render($view, $parameters);
	}
}
