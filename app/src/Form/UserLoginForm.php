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
				"label" => "Email",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(message: "L'adresse email est obligatoire"),
					new Assert\Email(message: "L'adresse email n'est pas valide"),
				],
			])
			->add("password", PasswordType::class, [
				"attr" => [
					// "pattern" => "[^]{8,}",
				],
				"label" => "Mot de passe",
				"label_attr" => [
					"class" => "form-label",
				],
				"constraints" => [
					new Assert\NotBlank(message: "Le mot de passe est obligatoire"),
					new Assert\Length([
						"min" => 8,
						"minMessage" => "Le mot de passe doit faire au moins {{ limit }} caractères",
					]),
				],
			])
			->add("submit", SubmitType::class, [
				"label" => "Se connecter",
			]);
	}
}
