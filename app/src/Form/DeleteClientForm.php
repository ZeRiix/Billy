<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteClientForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("client", EntityType::class, [
				"class" => Client::class,
				"choice_label" => "name",
				"label" => "Client à supprimer",
				"choices" => $options["clients"],
			])
			->add("submit", SubmitType::class, [
				"label" => "Supprimer",
				"attr" => [
					"class" => "btn btn-danger",
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			"clients" => null,
		]);
	}
}
