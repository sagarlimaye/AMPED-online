<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Entity\AmpedTextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class TICFormType extends AbstractType
{
//    private $questions;
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $questions = $options['questions'];
        foreach ($questions as $key=>$question) {
            $builder->add('question'.$key, AmpedTextareaType::class, array('label'=>$question->getText(), 'required' => false,
                'attr' => ['class' => 'btn']
                ));
        }
                $builder->add('submit', SubmitType::class, ['label'=>'Submit', 'attr'=>['id'=>'next'] ]);                    
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'questions' => null
        ));
    }
}
