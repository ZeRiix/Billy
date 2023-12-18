<?php

namespace App\Services;

use App\Entity\Organization;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Exception;

class ServiceService
{
	private ServiceRepository $serviceRepository;

	public function __construct(ServiceRepository $serviceRepository)
	{
		$this->serviceRepository = $serviceRepository;
	}

	public function createService(Organization $organization, Service $service)
	{
		$findedService = $this->serviceRepository->findByName($organization, $service->getName());
		if ($findedService) {
			throw new Exception("Name already use.");
		}

		$service->setOrganization($organization);

		$this->serviceRepository->save($service);
	}

	public function updateService(Organization $organization, Service $service)
	{
		$findedService = $this->serviceRepository->findByName($organization, $service->getName());
		if ($findedService) {
			throw new Exception("Name already use.");
		}

		$service->setOrganization($organization);

		$this->serviceRepository->save($service);
	}
}