<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateServiceForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("name", TextType::class, [
				"label" => "Nom",
			])
			->add("description", TextareaType::class, [
				"label" => "Description",
			])
			->add("submit", SubmitType::class, [
				"label" => "Valider",
			]);
	}
}
