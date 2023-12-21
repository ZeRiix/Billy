<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template T
 */
abstract class BaseRepository extends ServiceEntityRepository
{
	/**
	 * @param class-string<T> $entityClass
	 */
	public function __construct(ManagerRegistry $registry, $entityClass)
	{
		parent::__construct($registry, $entityClass);
	}

	/**
	 * @param string $id
	 * @return T|null
	 */
	public function getById(string $id): ?object // T
	{
		return $this->findOneBy(['id' => $id]);
	}

	/**
	 * @return T[]
	 */
	public function getAll()
	{
		return $this->findAll();
	}

	/**
	 * @param T $entity
	 */
	public function save($entity)
	{
		$this->_em->persist($entity);
		$this->_em->flush();

		return $entity;
	}

	/**
	 * @param void
	 */
	public function delete($entity)
	{
		$this->_em->remove($entity);
		$this->_em->flush();
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function deleteById(string $id): void
	{
		$entity = $this->getById($id);
		if ($entity === null) {
			throw new \Exception('Entity not found', Response::HTTP_NOT_FOUND);
		}
		$this->delete($entity);
	}

	/**
	 * @param T $entity
	 */
	public function update($entity)
	{
		$this->_em->flush();

		return $entity;
	}
}