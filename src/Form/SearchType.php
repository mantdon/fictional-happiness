<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SearchType as Search;
use Symfony\Component\Validator\Constraints\Regex;


class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', Search::class, array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'Paieškos laukelis negali būti tuščias',
                    )),
                    new Regex(array(
                        'pattern' => '/^[a-zA-Z0-9\s]*$/',
                        'message'   => 'Paieškos raktą gali sudaryti raidės, skaičiai ir tarpai'
                    ))
                ),

            ))
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['attr'=> array('novalidate'=>'novalidate')]);
    }
}