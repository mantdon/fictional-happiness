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
			->add('make', TextType::class, array('label' => 'Markė', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
			->add('model', TextType::class, array('label' => 'Modelis', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
			->add('year_of_production', TextType::class, array('label' => 'Pagaminimo metai', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
			->add('plate_number', TextType::class, array('label' => 'Numeriai', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
			->add('submit', SubmitType::class, array('label' => 'Pridėti automobilį', 'attr' => array('class' => 'btn btn-primary')));
	}

	public function configureOptions( OptionsResolver $resolver )
	{
		$resolver->setDefaults(['data_class' => Vehicle::class]);
	}
}