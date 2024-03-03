<?php

namespace App\Repository\Traits;

trait SaveTrait
{
    public function save($entity, $flush = true)
    {
		$this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }
}