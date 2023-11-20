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
					"class" => "form-control",
					"minlenght" => "2",
					"maxlenght" => "100",
				],
				"label" => "Nom de l'Organization",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 2, "max" => 100]),
				],
			])
			->add("adress", TextType::class, [
				"attr" => [
					"class" => "form-control",
					"minlenght" => "2",
					"maxlenght" => "255",
				],
				"label" => "Adresse de l'Organization",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 2, "max" => 255]),
				],
			])
			->add("email", EmailType::class, [
				"attr" => [
					"class" => "form-control",
					"minlenght" => "2",
					"maxlenght" => "180",
				],
				"label" => "Adresse email",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 2, "max" => 180]),
				],
			])
			->add("phone", TelType::class, [
				"attr" => [
					"class" => "form-control",
					"minlenght" => "10",
					"maxlenght" => "10",
				],
				"label" => "Téléphone de l'Organization",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 10, "max" => 10]),
				],
			])
			->add("activity", TextType::class, [
				"attr" => [
					"class" => "form-control",
					"minlenght" => "2",
					"maxlenght" => "100",
				],
				"label" => "Activité de l'Organization",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 2, "max" => 100]),
				],
			])
			->add("siret", IntegerType::class, [
				"required" => true,
				"attr" => [
					"class" => "form-control",
					"minlenght" => "14",
					"maxlenght" => "14",
				],
				"label" => "Siret de l'Organization",
				"label_attr" => [
					"class" => "form_label",
				],
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 14, "max" => 14]),
				],
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" => "btn btn-primary",
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Organization::class,
		]);
	}
}
