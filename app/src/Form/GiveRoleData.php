<?php
namespace App\Form;

use App\Entity\User;
use App\Entity\Role;

class GiveRoleData
{
	public ?User $user = null;
	public ?Role $role = null;

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function getRole(): ?Role
	{
		return $this->role;
	}
}
