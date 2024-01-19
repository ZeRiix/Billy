<?php 

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;	
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDevisForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$organizationId = $options["organization_id"];
		$builder
			->add("name", TextType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Nom du devis",
				"label_attr" => [
					"class" => "form-label",
				],
			])
			->add("description", TextareaType::class, [
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Description du devis",
				"label_attr" => [
					"class" => "form-label",
				],
			])
			->add("client", EntityType::class, [
				"class" => Client::class,
				"choice_label" => "name",
				"query_builder" => function (ClientRepository $clientRepository) use ($organizationId): QueryBuilder {
					return $clientRepository->createQueryBuilder("c")
											->orderBy("c.name", "ASC")
											->where("c.organization = :organization")
											->setParameter("organization", $organizationId);
				},
			])
			->add("discount", IntegerType::class, [
				"required" => false,
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Remise (en €)",
			])
			->add("submit", SubmitType::class, [
				"label" => "Valider",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Devis::class,
			"organization_id" => null,
		]);
	}
}