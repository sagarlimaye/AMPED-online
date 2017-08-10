<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class BehaviourType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add(
                        'day', DateTimeType::class, 
                        [
                            'with_minutes' => false,
                            'with_seconds' => false
                        ]
                     )
                ->add('behaviour', TextareaType::class)
                ->add('self_talk', TextareaType::class)
                ->add('stress_level', ChoiceType::class,
                        [
                            'choices' => [
                                'High' => 3,
                                'Medium' => 2,
                                'Low' => 1
                            ]
                        ]
                     );
    }
    public function getParent()
    {
        return FormType::class;
    }
}
