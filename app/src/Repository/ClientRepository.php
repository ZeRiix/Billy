<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BaseRepository;

/**
 * @extends ClientEntityRepository<Client>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends BaseRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Client::class);
	}

	public function create(array $data): Client
	{
		$client = new Client();

		$client->setFirstName($data["firstName"]);
		$client->setName($data["name"]);
		$client->setAddress($data["address"]);
		$client->setEmail($data["email"]);
		$client->setPhone($data["phone"]);
		$client->setAddress($data["address"]);
		$client->setActivity($data["activity"]);
		$client->setOrganization($data["Organization"]);

		$this->save($client);

		return $client;
	}

	public function findOneBySiret(string $siret): ?Client
	{
		return $this->findOneBy(["siret" => $siret]);
	}
}
