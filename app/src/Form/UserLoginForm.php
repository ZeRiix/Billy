<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserLoginForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
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
				"constraints" => [
					new Assert\NotBlank(message: "L'adresse email est obligatoire"),
					new Assert\Email(message: "L'adresse email n'est pas valide"),
				],
			])
			->add("password", PasswordType::class, [
				"attr" => [
					"class" =>
						"w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
					// "pattern" => "[^]{8,}",
				],
				"label" => "Mot de passe",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [
					new Assert\NotBlank(message: "Le mot de passe est obligatoire"),
					new Assert\Length([
						"min" => 8,
						"minMessage" => "Le mot de passe doit contenir au moins 8 caractères",
					]),
				],
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
				],
				"label" => "Se connecter",
			]);
	}
}
