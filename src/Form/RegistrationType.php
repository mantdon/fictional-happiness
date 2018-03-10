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
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Slaptažodis'),
                'second_options' => array('label' => 'Pakartoti slaptažodį'),
                'invalid_message' => 'Slaptažodžiai nesutampa'
            ))
            ->add('first_name', TextType::class, array('label' => 'Vardas'))
            ->add('last_name', TextType::class, array('label' => 'Pavardė'))
            ->add('phone', TextType::class, array('label' => 'Telefonas'))
            ->add('city', TextType::class, array('label' => 'Miestas'))
            ->add('address', TextType::class, array('label' => 'Adresas'))
            ->add('submit', SubmitType::class, array('label' => 'Registruotis'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'attr'=> array('novalidate'=>'novalidate')
        ));
    }
}