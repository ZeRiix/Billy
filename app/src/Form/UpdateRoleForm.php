<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateRoleForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("name", TextType::class, ["label" => "Nom du rôle"])
			->add("manage_org", CheckboxType::class, ["label" => "Manage Organization", "required" => false])
			->add("manage_user", CheckboxType::class, ["label" => "Manage User", "required" => false])
			->add("manage_client", CheckboxType::class, ["label" => "Manage Client", "required" => false])
			->add("write_devis", CheckboxType::class, ["label" => "Write Devis", "required" => false])
			->add("write_factures", CheckboxType::class, ["label" => "Write Facture", "required" => false])
			->add("manage_service", CheckboxType::class, ["label" => "Manage Service", "required" => false])
			->add("submit", SubmitType::class, ["label" => "Éditer"]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Role::class,
		]);
	}
}
