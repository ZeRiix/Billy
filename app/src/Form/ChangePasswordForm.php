<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("password", RepeatedType::class, [
				"type" => PasswordType::class,
				"first_options" => [
					"attr" => [
						"class" => "",
						"pattern" => "[^]{8,}",
					],
					"label" => "Mot de passe",
					"label_attr" => [
						"class" => "",
					],
					"required" => true,
				],
				"second_options" => [
					"attr" => [
						"class" => "",
						"pattern" => "[^]{8,}",
					],
					"label" => "Confirmé mot de passe",
					"label_attr" => [
						"class" => "",
					],
					"required" => true,
				],
				"invalid_message" => "Les mots de passe ne correspondent pas.",
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 8]),
				],
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" => "",
					"text" => "Créer mon compte",
				],
			]);
	}
}
