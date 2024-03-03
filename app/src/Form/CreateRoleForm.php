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
			])
			->add("manage_org", CheckboxType::class, [
				"label" => "Gérer les organisations",
				"required" => false,
			])
			->add("manage_user", CheckboxType::class, [
				"label" => "Gérer les utilisateurs",
				"required" => false,
			])
			->add("manage_client", CheckboxType::class, [
				"label" => "Gérer les clients",
				"required" => false,
			])
			->add("write_devis", CheckboxType::class, [
				"label" => "Écrire des devis",
				"required" => false,
			])
			->add("write_factures", CheckboxType::class, [
				"label" => "Écrire des factures",
				"required" => false,
			])
			->add("manage_service", CheckboxType::class, [
				"label" => "Gérer les services",
				"required" => false,
			])
			->add("read_devis", CheckboxType::class, [
				"label" => "Lire les devis",
				"required" => false,
			])
			->add("read_factures", CheckboxType::class, [
				"label" => "Lire les factures",
				"required" => false,
			])
			->add("view_stats", CheckboxType::class, [
				"label" => "Voir les statistiques",
				"required" => false,
			])
			->add("submit", SubmitType::class, [
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
