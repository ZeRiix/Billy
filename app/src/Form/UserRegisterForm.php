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
					"maxLenght" => "100",
					"minLenght" => "2",
				],
				"label" => "Prénom",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(message: "Le prénom est obligatoire"),
					new Assert\Length([
						"min" => 2,
						"max" => 100,
						"minMessage" => "Le prénom doit faire au moins {{ limit }} caractères",
						"maxMessage" => "Le prénom doit faire au plus {{ limit }} caractères",
					]),
				],
			])
			->add("name", TextType::class, [
				"attr" => [
					"maxLenght" => "100",
					"minLenght" => "2",
				],
				"label" => "Nom",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(message: "Le nom est obligatoire"),
					new Assert\Length([
						"min" => 2,
						"max" => 100,
						"minMessage" => "Le nom doit faire au moins {{ limit }} caractères",
						"maxMessage" => "Le nom doit faire au plus {{ limit }} caractères",
					]),
				],
			])
			->add("email", EmailType::class, [
				"label" => "Email",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(message: "L'email est obligatoire"),
					new Assert\Email(message: "L'email n'est pas valide"),
				],
			])
			->add("password", RepeatedType::class, [
				"type" => PasswordType::class,
				"first_options" => [
					"attr" => [
						// "pattern" => "[^]{8,}",
					],
					"label" => "Mot de passe",
					"label_attr" => [
						"class" => "form-label",
					],
				],
				"second_options" => [
					"attr" => [
						// "pattern" => "[^]{8,}",
					],
					"label" => "Confirmer mot de passe",
					"label_attr" => [
						"class" => "form-label",
					],
					"required" => true,
				],
				"invalid_message" => "Les mots de passe ne correspondent pas.",
				"constraints" => [
					new Assert\NotBlank(message: "Le mot de passe est obligatoire"),
					new Assert\Length([
						"min" => 8,
						"minMessage" => "Le mot de passe doit faire au moins {{ limit }} caractères",
					]),
				],
			])
			->add("submit", SubmitType::class, [
				"label" => "S'inscrire",
			]);
	}
}
