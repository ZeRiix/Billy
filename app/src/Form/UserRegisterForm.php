<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("firstName", TextType::class, [
				"attr" => [
					"class" => "",
					"maxLenght" => "100",
					"minLenght" => "2",
				],
				"label" => "Prénom",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 2, "max" => 100]),
				],
			])
			->add("name", TextType::class, [
				"attr" => [
					"class" => "",
					"maxLenght" => "100",
					"minLenght" => "2",
				],
				"label" => "Nom",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [
					new Assert\NotBlank(),
					new Assert\Length(["min" => 2, "max" => 100]),
				],
			])
			->add("email", EmailType::class, [
				"attr" => [
					"class" => "",
				],
				"label" => "Email",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [new Assert\NotBlank(), new Assert\Email()],
			])
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
