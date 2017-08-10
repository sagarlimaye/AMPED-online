<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\AmpedTextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class MAFFormType extends AbstractType
{
//    private $questions;
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $questions = $options['questions'];
        foreach ($questions as $key=>$question) {
            $builder->add('question'.$key, AmpedTextareaType::class, array('label'=>$question->getText()));
        }
        $builder->add('agreeTerms', CheckboxType::class, array('label'=>'We agree to work together for the next few weeks', 'mapped'=>false))
                ->add('protegeSign', TextType::class, array('mapped'=>false))
                ->add('mentorSign', TextType::class, array('mapped'=>false))
                ->add('submit', SubmitType::class, ['label'=>'Submit', 'attr'=>['id'=>'next']]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'questions' => null
        ));
    }
}
