<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
				"required" => false,
			])
			->add("firstname", TextType::class, [
				"label" => "Prénom",
				"required" => false,
			])
			->add("address", TextType::class, [
				"label" => "Adresse",
				"required" => false,
			])
			->add("email", EmailType::class, [
				"label" => "Email",
			])
			->add("phone", TelType::class, [
				"required" => false,
				"label" => "Téléphone",
			])
			->add("activity", TextType::class, [
				"label" => "Activité",
				"required" => false,
			])
			->add("siret", IntegerType::class, [
				"label" => "SIRET",
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
