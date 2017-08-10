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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class ABCType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('active_event', TextareaType::class)
                ->add('neg_belief', TextareaType::class)
                ->add('pos_belief', TextareaType::class)
                ->add('neg_cons', TextareaType::class)
                ->add('pos_cons', TextareaType::class);
    }
    public function getParent()
    {
        return FormType::class;
    }
}
