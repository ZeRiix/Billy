<?php

namespace App\Middleware;

use App\Repository\OrganizationRepository;
use Symfony\Component\HttpFoundation\Request;

class GetOrganizationMiddleware extends AbstractMiddleware
{
	private OrganizationRepository $organizationRepository;

	private Request $req;

	public function __construct(OrganizationRepository $organizationRepository, Request $req)
	{
		$this->req = $req;
		$this->organizationRepository = $organizationRepository;
	}

	public function handler(mixed $input, ?array $options): mixed
	{
		// use always OrganizationId name pramater in query string
		// exemple: /dashboard/{OrganizationId}
		$organization = $this->organizationRepository->getById($this->req->get("OrganizationId"));
		if ($organization) {
			return $this->output("exist", $organization);
		} else {
			return $this->output("organization notfound");
		}
	}
}
