<?php

namespace App\Form;

use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DeleteRoleForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$organizationId = $options["organization_id"];

		$builder
			->add("role", EntityType::class, [
				"class" => Role::class,
				"choice_label" => "name",
				"label" => "Sélectionnez le rôle à supprimer",
				"query_builder" => function (\Doctrine\ORM\EntityRepository $er) use (
					$organizationId
				) {
					return $er
						->createQueryBuilder("r")
						->innerJoin("r.organization", "o")
						->where("o.id = :organizationId")
						->andWhere("r.name != 'OWNER'")
						->setParameter("organizationId", $organizationId);
				},
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
				],
				"label" => "Supprimer",
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			"data_class" => null,
			"organization_id" => null,
		]);
	}
}
