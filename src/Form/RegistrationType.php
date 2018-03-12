<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('label' => 'El-paštas', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Slaptažodis', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')),
                'second_options' => array('label' => 'Pakartoti slaptažodį', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')),
                'invalid_message' => 'Slaptažodžiai nesutampa'
            ))
            ->add('first_name', TextType::class, array('label' => 'Vardas', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('last_name', TextType::class, array('label' => 'Pavardė', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('phone', TextType::class, array('label' => 'Telefonas', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('city', TextType::class, array('label' => 'Miestas', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('address', TextType::class, array('label' => 'Adresas', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('submit', SubmitType::class, array('label' => 'Registruotis', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'attr'=> array('novalidate'=>'novalidate')
        ));
    }
}