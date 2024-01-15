<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add("firstName", TextType::class, [
				"label" => "Prénom",
			])
			->add("name", TextType::class, [
				"label" => "Nom",
			])
            ->add("email", EmailType::class, [
				"label" => "Email",
			])
            ->add("password", RepeatedType::class, [
				"type" => PasswordType::class,
				"first_options" => [
					"attr" => [
						// "pattern" => "[^]{8,}",
					],
					"label" => "Mot de passe",
				],
				"second_options" => [
					"attr" => [
						// "pattern" => "[^]{8,}",
					],
					"label" => "Confirmer mot de passe",
				],
			])
			->add("submit", SubmitType::class, [
				"label" => "S'inscrire",
			]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
