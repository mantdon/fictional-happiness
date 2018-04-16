<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class VehicleType extends AbstractType{
	public function buildForm( FormBuilderInterface $builder, array $options )
	{
		$builder
			->add('make', TextType::class)
			->add('model', TextType::class)
			->add('year_of_production', TextType::class)
			->add('plate_number', TextType::class)
			->add('submit', SubmitType::class);
	}

	public function configureOptions( OptionsResolver $resolver )
	{
		$resolver->setDefaults(['data_class' => Vehicle::class]);
	}
}