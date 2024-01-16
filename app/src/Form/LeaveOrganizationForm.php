<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeaveOrganizationForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add("submit", SubmitType::class, [
			"label" => 'Quitter l\'organisation',
		]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([]);
	}
}