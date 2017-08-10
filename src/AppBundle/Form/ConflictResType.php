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

/**
 * Description of TICFormType
 *
 * @author sagar
 */
class ConflictResType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('question1', TextareaType::class, 
                        ['label' => 'You were late to class because you had to help a new student find their classroom. When you got to class your teacher gave you a mark and assigned you detention. What strategy would you use in this situation and why? (Think: What outcome do you hope to achieve?)'])
                ->add('question2', TextareaType::class, 
                        ['label' => 'A student in your homeroom class insulted your friend and you are really mad. You do not like the person anyway and this adds to your feelings of dislike. What strategy would you use in this situation and why? (Think: What outcome do you hope to achieve?)'])
                ->add('Submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }
}
