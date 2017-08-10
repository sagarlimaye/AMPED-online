<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class GPAWorksheetType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('interactions', CollectionType::class, 
                        [
                            'entry_type' => TextareaType::class,
                            'allow_add'    => true,
                            'allow_delete' => true,
                            'prototype'    => true,
                            'required'     => false,
                            'attr'         => [
                                'class' => 'interactions-collection',
                            ]                            
                        ])
                ->add('achievements', CollectionType::class,
                        [
                            'entry_type' => TextareaType::class,
                            'allow_add'    => true,
                            'allow_delete' => true,
                            'prototype'    => true,
                            'required'     => false,
                            'attr'         => [
                                'class' => 'achievements-collection',
                            ]                            
                        ])
                ->add('pos_activities', TextareaType::class)
                ->add('gpa', IntegerType::class)
                ->add('submit', SubmitType::class, ['label' => 'Submit']);                ;
    }
}
