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
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
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
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
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
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
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
						"class" =>
							"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
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
						"class" =>
							"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
						"pattern" => "[^]{8,}",
					],
					"label" => "Confirmer mot de passe",
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
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
					"text" => "Valider",
				],
			]);
	}
}
