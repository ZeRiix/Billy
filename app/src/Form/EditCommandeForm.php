<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditCommandeForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) : void
	{
		$organizationId = $options["organization_id"];
		$builder
			->add("name", TextType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Nom de la commande",
			])
			->add("description", TextareaType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Description de la commande",
			])
			->add("unitPrice", NumberType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Prix unitaire du service (en €)",
			])
			->add("quantity", IntegerType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Quantité",
			])
			->add("service", EntityType::class, [
				"class" => Service::class,
				"choice_label" => "name",
				"query_builder" => function (ServiceRepository $serviceRepository) use ($organizationId): QueryBuilder {
					return $serviceRepository->createQueryBuilder("s")
											->orderBy("s.name", "ASC")
											->where("s.organization = :organization")
											->setParameter("organization", $organizationId);
				},
			])
			->add("submit", SubmitType::class, [
				"label" => "Modifier la commande",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Commande::class,
			"organization_id" => null,
		]);
	}
}