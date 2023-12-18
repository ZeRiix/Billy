<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\GiveRoleData;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiveRoleForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$organizationId = $options["organization_id"];

		$builder
			->add("user", EntityType::class, [
				"class" => User::class,
				"choice_label" => "name",
				"label" => "Utilisateur",
				"query_builder" => function (\Doctrine\ORM\EntityRepository $er) use (
					$organizationId
				) {
					return $er
						->createQueryBuilder("u")
						->innerJoin("u.organizations", "o")
						->where("o.id = :organizationId")
						->setParameter("organizationId", $organizationId);
				},
			])
			->add("role", EntityType::class, [
				"class" => Role::class,
				"choice_label" => "name",
				"label" => "Rôle",
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
				"label" => "Donner",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => GiveRoleData::class,
			"organization_id" => null,
		]);
	}
}
