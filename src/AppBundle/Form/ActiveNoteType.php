<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Description of TICFormType
 *
 * @author sagar
 */
class ActiveNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('question1', ChoiceType::class, 
                        [
                            'label' => 'Do you complete the assigned readings before each class?',
                            'choices' => ['Always' => 'Always', 'Sometimes' => 'Sometimes', 'Never' => 'Never']
                        ])
                ->add('question2', ChoiceType::class, 
                        [
                            'label' => 'Do you try to sit as close as possible to the teacher?',
                            'choices' => ['Always' => 'Always', 'Sometimes' => 'Sometimes', 'Never' => 'Never']
                        ])
                ->add('question3', ChoiceType::class, 
                        [
                            'label' => 'Do you doodle during a class?',
                            'choices' => ['Always' => 'Always', 'Sometimes' => 'Sometimes', 'Never' => 'Never']
                        ])
                ->add('question4', ChoiceType::class, 
                        [
                            'label' => 'Do you avoid listening when difficult information is presented?',
                            'choices' => ['Always' => 'Always', 'Sometimes' => 'Sometimes', 'Never' => 'Never']
                        ]);
    }
}
