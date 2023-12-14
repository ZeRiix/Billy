<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateRoleForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("name", null, [
				"label" => "Nom du rôle",
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
			])
			->add("manage_org", CheckboxType::class, [
				"label" => "Gérer les organisations",
				"required" => false,
				"attr" => [
					"class" => "form-check-input",
				],
			])
			->add("manage_user", CheckboxType::class, [
				"label" => "Gérer les utilisateurs",
				"required" => false,
				"attr" => [
					"class" => "form-check-input",
				],
			])
			->add("manage_client", CheckboxType::class, [
				"label" => "Gérer les clients",
				"required" => false,
				"attr" => [
					"class" => "form-check-input",
				],
			])
			->add("write_devis", CheckboxType::class, [
				"label" => "Écrire des devis",
				"required" => false,
				"attr" => [
					"class" => "form-check-input",
				],
			])
			->add("write_factures", CheckboxType::class, [
				"label" => "Écrire des factures",
				"required" => false,
				"attr" => [
					"class" => "form-check-input",
				],
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
				],
				"label" => "Créer",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Role::class,
		]);
	}
}
