<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class EditOrganizationForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("activity", TextType::class, [
				"required" => false,
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Activité",
				"label_attr" => [
					"class" => "form-label",
				],
			])
			->add("email", EmailType::class, [
				"required" => false,
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Adresse email",
				"label_attr" => [
					"class" => "form-label",
				],
			])
			->add("phone", TelType::class, [
				"required" => false,
				"attr" => [
					"class" => "w-80 p-2 rounded-lg outline-none border-solid border-2 focus:border-bgreen",
				],
				"label" => "Téléphone",
				"label_attr" => [
					"class" => "form-label",
				],
			])
			->add('logoFile', VichFileType::class, [
				'required' => false,
				'allow_delete' => true,
				'delete_label' => '...',
				'download_uri' => '...',
				'download_label' => '...',
				'asset_helper' => true,
			])
			->add("submit", SubmitType::class, [
				"attr" => [
					"class" =>
						"px-12 py-4 text-white text-lg font-semibold bg-blighter-green rounded-large hover:bg-bgreen ease-in-out duration-300",
				],
				"label" => "Modifier",
			]);
	}
}
