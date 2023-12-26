<?php

namespace App\Form;

use App\Entity\Organization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrganizationForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("email", EmailType::class, [
				"attr" => [
					"minlenght" => "2",
					"maxlenght" => "180",
				],
				"label" => "Adresse email",
			])
			->add("phone", TelType::class, [
				"attr" => [
					"minlenght" => "10",
					"maxlenght" => "10",
				],
				"label" => "Téléphone",
			])
			->add("activity", TextType::class, [
				"attr" => [
					"minlenght" => "2",
					"maxlenght" => "100",
				],
				"label" => "Secteur d'activité",
			])
			->add("siret", IntegerType::class, [
				"attr" => [
					"minlenght" => "14",
					"maxlenght" => "14",
				],
				"label" => "Numéro de siret",
			])
			->add("submit", SubmitType::class, [
				"label" => "Ajouter",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Organization::class,
		]);
	}
}
