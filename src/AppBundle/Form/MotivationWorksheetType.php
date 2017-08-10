<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


/**
 * Description of TICFormType
 *
 * @author sagar
 */
class MotivationWorksheetType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('motivation_place_1', TextareaType::class)
                ->add('motivation_place_2', TextareaType::class)
                ->add('motivation_place_3', TextareaType::class)
                ->add('rewards', TextareaType::class)
                ->add('motivation_chart', CollectionType::class,
                    [
                        'entry_type' => MotivationActivityType::class, 'allow_add' => true, 'allow_delete' => true,
                        'entry_options' => ['attr' => ['class' => 'item'] ],
                        'required'     => false,
                        'prototype' => true,
                        'delete_empty' => true,                    
                        'attr' => [
                            'class' => 'table table-responsive motivation-activity-collection'
                        ]
                    ])
                
                ->add('submit', SubmitType::class, ['label' => 'Submit']);
    }
}
