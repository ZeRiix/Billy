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

class CreateOrganizationFormType extends AbstractType
{
	public function buildForm(
		FormBuilderInterface $builder,
		array $options
	): void {
		$builder
			->add("name", TextType::class, [
				"required" => true,
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-green",
					"minlenght" => "2",
					"maxlenght" => "100",
				],
				"label" => "Nom de l'organisation",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(
						message: "Le nom de l'organisation est obligatoire"
					),
					new Assert\Length([
						"min" => 2,
						"max" => 100,
						"minMessage" =>
							'Le nom de l\'organisation doit faire au moins {{ limit }} caractères',
						"maxMessage" =>
							'Le nom de l\'organisation doit faire au plus {{ limit }} caractères',
					]),
				],
			])
			->add("address", TextType::class, [
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-green",
					"minlenght" => "2",
					"maxlenght" => "255",
				],
				"label" => "Adresse",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(message: "L'adresse est obligatoire"),
					new Assert\Length([
						"min" => 2,
						"max" => 255,
						"minMessage" =>
							"L'adresse doit faire au moins {{ limit }} caractères",
						"maxMessage" =>
							"L'adresse doit faire au plus {{ limit }} caractères",
					]),
				],
			])
			->add("email", EmailType::class, [
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-green",
					"minlenght" => "2",
					"maxlenght" => "180",
				],
				"label" => "Adresse email",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(
						message: "L'adresse email est obligatoire"
					),
					new Assert\Length([
						"min" => 2,
						"max" => 180,
						"minMessage" =>
							"L'adresse email doit faire au moins {{ limit }} caractères",
						"maxMessage" =>
							"L'adresse email doit faire au plus {{ limit }} caractères",
					]),
				],
			])
			->add("phone", TelType::class, [
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-green",
					"minlenght" => "10",
					"maxlenght" => "10",
				],
				"label" => "Téléphone",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(
						message: "Le téléphone est obligatoire"
					),
					new Assert\Length([
						"min" => 10,
						"max" => 10,
						"exactMessage" =>
							"Le numéro de téléphone doit faire {{ limit }} caractères",
					]),
				],
			])
			->add("activity", TextType::class, [
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-green",
					"minlenght" => "2",
					"maxlenght" => "100",
				],
				"label" => "Secteur d'activité",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(
						message: "Le secteur d'activité est obligatoire"
					),
					new Assert\Length([
						"min" => 2,
						"max" => 100,
						"minMessage" =>
							"Le secteur d'activité doit faire au moins {{ limit }} caractères",
						"maxMessage" =>
							"Le secteur d'activité doit faire au plus {{ limit }} caractères",
					]),
				],
			])
			->add("siret", IntegerType::class, [
				"required" => true,
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-green",
					"minlenght" => "14",
					"maxlenght" => "14",
				],
				"label" => "Numéro de siret",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(message: "Le siret est obligatoire"),
					new Assert\Length([
						"min" => 14,
						"max" => 14,
						"exactMessage" =>
							"Le siret doit faire {{ limit }} caractères",
					]),
				],
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-lighter-green rounded-large hover:bg-green ease-in-out duration-300",
				],
				"label" => "Créer",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Organization::class,
		]);
	}
}
