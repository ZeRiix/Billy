<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateClientForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("name", TextType::class, [
				"label" => "Nom",
				"attr" => [
					"placeholder" => "Nom du client",
				],
				"required" => false,
			])
			->add("firstname", TextType::class, [
				"label" => "Prénom",
				"attr" => [
					"placeholder" => "Prénom du client",
				],
				"required" => false,
			])
			->add("address", TextareaType::class, [
				"label" => "Adresse",
				"attr" => [
					"placeholder" => "Adresse du client",
				],
				"required" => false,
			])
			->add("email", EmailType::class, [
				"label" => "Email",
				"attr" => [
					"placeholder" => "Email du client",
				],
			])
			->add("phone", TelType::class, [
				"required" => false,
				"label" => "Téléphone",
				"attr" => [
					"placeholder" => "Numéro de téléphone du client",
				],
			])
			->add("activity", TextType::class, [
				"label" => "Activité",
				"attr" => [
					"placeholder" => "Activité du client",
				],
				"required" => false,
			])
			->add("siret", IntegerType::class, [
				"label" => "SIRET",
				"attr" => [
					"placeholder" => "Numéro SIRET du client",
				],
				"required" => false,
			])
			->add("submit", SubmitType::class, [
				"label" => "Valider",
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			"data_class" => Client::class,
		]);
	}
}
