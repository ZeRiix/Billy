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
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" => "",
					"text" => "Créer mon compte",
				],
			]);
	}
}
