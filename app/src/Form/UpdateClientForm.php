<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateClientForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("name", TextType::class, ["label" => "Nom"])
			->add("firstname", TextType::class, ["label" => "Prénom"])
			->add("email", EmailType::class, ["label" => "Email"])
			->add("phone", TelType::class, ["label" => "Téléphone"])
			->add("activity", TextareaType::class, ["label" => "Activité"])
			->add("address", TextareaType::class, ["label" => "Adresse"])
			->add("submit", SubmitType::class, ["label" => "Enregistrer les modifications"]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Client::class,
		]);
	}
}