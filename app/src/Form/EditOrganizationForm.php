<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Organization;

class EditOrganizationForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("activity", TextType::class, [
				"required" => false,
				"label" => "Activité",
			])
			->add("email", EmailType::class, [
				"required" => false,
				"label" => "Adresse email",
				"label_attr" => [
					"class" => "form-label",
				]
			])
			->add("phone", TelType::class, [
				"required" => false,
				"label" => "Téléphone",
			])
			->add('logoFile', VichImageType::class, [
                'required' => false,
				"label" => "Logo",
                'allow_delete' => true,
				'delete_label' => 'Supprimer',
                'download_uri' => true,
				'download_label' => 'Télécharger',
                'image_uri' => true,
            ])
			->add("submit", SubmitType::class, [
				"label" => "Modifier",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => Organization::class,
		]);
	}
}
