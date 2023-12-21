<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ForgetPasswordForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("email", EmailType::class, [
				"label" => "Email",
				"label_attr" => [
					"class" => "form-label",
				],
				"required" => true,
				"constraints" => [
					new Assert\NotBlank(message: "L'adresse email est obligatoire"),
					new Assert\Email(message: "L'adresse email n'est pas valide"),
				],
			])
			->add("submit", SubmitType::class, [
				"label" => "Envoyer",
			]);
	}
}
