<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\ABCType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * Description of TICFormType
 *
 * @author sagar
 */
class ABCWorksheetType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('abc_entries', CollectionType::class,
                        [
                            'entry_type' => ABCType::class, 'allow_add' => true,
                            'allow_delete' => true,
                            'entry_options' => ['attr' => ['class' => 'item'] ],
                            'required'     => false,
                            'prototype' => true,
                            'delete_empty' => true,     
                            'label' => false,
                            'attr' => [
                                'class' => 'table table-responsive abc-collection'
                                ]
                        ]
                        )
                ->add('Submit', SubmitType::class);
    }
    public function getBlockPrefix() {
        return 'abcworksheet';
    }
}
