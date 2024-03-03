<?php

namespace App\Form;

use App\Entity\BillReminder;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateBillReminderForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add("date_send", DateType::class, [
				"label" => "Date d'envoi",
				"widget" => "single_text",
				"format" => "yyyy-MM-dd",
			])
			->add("submit", SubmitType::class, [
				"label" => "Créer un rappel de facture",
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			"data_class" => BillReminder::class,
		]);
	}
}
