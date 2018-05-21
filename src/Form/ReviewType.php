<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ReviewType extends AbstractType {
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add('content', TextareaType::class)
            ->add('rating',  ChoiceType::class, array(
                'choices'  => array(
                    '5' => 5,
                    '4' => 4,
                    '3' => 3,
                    '2' => 2,
                    '1' => 1
                ),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('submit', SubmitType::class);
    }
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
            'attr'=> array('novalidate'=>'novalidate')
        ]);
    }
}