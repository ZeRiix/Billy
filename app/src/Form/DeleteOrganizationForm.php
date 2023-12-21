<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Organization;

class DeleteOrganizationForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("organization", EntityType::class, [
				"class" => Organization::class,
				"choice_label" => "name",
				"label" => 'Sélectionnez l\'organisation à supprimer',
				"choices" => $options["organizations"],
			])
			->add("submit", SubmitType::class, [
				"label" => "Supprimer",
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			"organizations" => null,
		]);
	}
}
