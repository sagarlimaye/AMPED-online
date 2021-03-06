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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class ScrolWorksheetType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('scrol_answer', TextareaType::class, ['label' => 'Identify current homework or study materials the student could use the SCROL method with, and write them in the box below.'])
                ->add('Submit', SubmitType::class);
    }
    public function getParent()
    {
        return FormType::class;
    }
}
