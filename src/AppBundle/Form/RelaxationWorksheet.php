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
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class RelaxationWorksheet extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('situations', TextareaType::class)
                ->add('Submit', SubmitType::class);
    }
    
}
