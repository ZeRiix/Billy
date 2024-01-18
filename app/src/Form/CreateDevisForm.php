<?php

namespace App\Form;

use App\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateDevisForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("name", TextType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Nom du devis",
				"label_attr" => [
					"class" => "form-label",
				],
			])
			->add("submit", SubmitType::class, [
				"label" => "Créer un devis",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Devis::class,
		]);
	}
}