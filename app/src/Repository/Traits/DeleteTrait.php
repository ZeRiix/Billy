<?php

namespace App\Repository\Traits;

trait DeleteTrait
{
    public function delete($entity, $flush = true)
	{
		$this->_em->remove($entity);

		if ($flush) {
			$this->_em->flush();
		}
	}
}