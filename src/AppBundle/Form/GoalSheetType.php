<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class GoalSheetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
//                ->add('date', DateType::class, 
//                        ['label' => 'Weekly Goal Sheet for: '])
                ->add('question2', TextareaType::class, 
                        ['label' => 'My Goal is to...'])
                ->add('things_done', TextareaType::class, [
                    'label' => 'Things I am already doing that will help me reach my goal: '
                     ])
                ->add('plan', TextareaType::class, [
                    'label' => 'My plan to help me reach my goal: '
                ])
                ->add('obstacle_solution', CollectionType::class, [
                    'entry_type' => ObstacleSolutionType::class,
                    'entry_options' => ['attr' => ['class' => 'item'] ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required'     => false,
                    'prototype' => true,
                    'delete_empty' => true,                    
                    'label' => 'Obstacles and solutions',
                    'attr' => [
                        'class' => 'table table-responsive obstacle-solution-collection'
                    ]
                ])
                ->add('plan_valid', TextareaType::class, [
                    'label' => 'How will I know if my plan is working?'
                ])
                ->add('student_sign', TextType::class, ['label'=>"Student's signature: ", 'mapped'=>false])
                ->add('mentor_sign', TextType::class, ['label'=>"Mentor's signature: ", 'mapped'=>false])
                ->add('submit', SubmitType::class, ['label' => 'Submit']);
                
    }
    public function getBlockPrefix()
    {
        return 'goalsheet';
    }    
}
