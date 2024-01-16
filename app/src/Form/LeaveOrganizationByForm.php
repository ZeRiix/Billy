<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeaveOrganizationByForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("user", EntityType::class, [
				"class" => User::class,
				"choices" => $options["users"],
				"choice_label" => "name",
				"label" => "Utilisateur",
			])
			->add("submit", SubmitType::class, [
				"label" => 'Quitter l\'organisation',
				"attr" => [
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"users" => null,
		]);
	}
}