<?php

namespace App\Form;

use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateServiceForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("name", TextType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
					"maxLenght" => "100",
					"minLenght" => "3",
				],
				"label" => "Nom",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [
					new Assert\NotBlank(message: "Le nom est oblgatoire"),
					new Assert\Length([
						"min" => 3,
						"max" => 100,
						"minMessage" => "Le nom doit faire au moins {{ limit }} caractères",
						"maxMessage" => "Le nom doit faire au plus {{ limit }} caractères",
					]),
				],
			])
			->add("description", TextareaType::class, [
				"attr" => [
					"class" =>
						"w-full h-36 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
					"maxLenght" => "1500",
					"minLenght" => "0",
				],
				"label" => "Description",
				"label_attr" => [
					"class" => "",
				],
				"required" => true,
				"constraints" => [
					new Assert\NotBlank(message: "La description est obligatoire"),
					new Assert\Length([
						"min" => 10,
						"max" => 1500,
						"minMessage" => "La description doit faire au moins {{ limit }} caractères",
						"maxMessage" => "La description doit faire au plus {{ limit }} caractères",
					]),
				],
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
				],
				"label" => "Modifier",
			]);
	}
}
