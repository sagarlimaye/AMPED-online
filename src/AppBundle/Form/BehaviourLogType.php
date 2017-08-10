<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * Description of TICFormType
 *
 * @author sagar
 */
class BehaviourLogType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('week', DateType::class)
                ->add('log', BehaviourType::class);
    }
}
