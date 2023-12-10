<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateServiceForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("name", TextType::class, [
				"attr" => [
					"class" => "",
					"maxLenght" => "100",
					"minLenght" => "3",
				],
				"label" => "Nom",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [new Assert\NotBlank(), new Assert\Length(["min" => 3, "max" => 100])],
			])
			->add("description", TextType::class, [
				"attr" => [
					"class" => "",
					"maxLenght" => "1500",
					"minLenght" => "0",
				],
				"label" => "Description",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [new Assert\NotBlank(), new Assert\Length(["min" => 0, "max" => 1500])],
			]);
	}
}
