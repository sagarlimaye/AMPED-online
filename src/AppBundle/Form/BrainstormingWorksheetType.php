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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Description of TICFormType
 *
 * @author sagar
 */
class BrainstormingWorksheetType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('brainstorm', CollectionType::class,
                        [
                            'entry_type' => BrainstormingType::class, 'allow_add' => true, 'allow_delete' => true,
                            'entry_options' => ['attr' => ['class' => 'item'] ],
                            'required'     => false,
                            'prototype' => true,
                            'delete_empty' => true,                    
                            'attr' => [
                                'class' => 'table table-striped brainstorming-collection'
                            ]
                        ])
                ->add('Submit', SubmitType::class);
    }
}
