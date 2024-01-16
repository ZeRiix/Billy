<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SelectRoleForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$roles = (array)$options["roles"];
		$rolesHas = (array)$options["rolesHas"];

		foreach ($roles as $role) {
			$isCheck = false;
        	foreach ($rolesHas as $roleHas) {
				if ($roleHas["id"] == $role->getId()) {
					$isCheck = true;
				}
			}
			if ($role->getName() === "OWNER") continue;
            $builder->add($role->getId(), CheckboxType::class, [
                'label' => $role->getName(),
                'data' => $isCheck,
                'required' => false,
            ]);
        }

		$builder->add("submit", SubmitType::class, [
			"attr" => [
				"class" =>
					"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
			],
			"label" => "Modifier",
		]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"roles" => null,
			"rolesHas" => null,
		]);
	}
}