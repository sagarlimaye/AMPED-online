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
use AppBundle\Entity\AmpedChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class SelfAssessmentFormType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $questions = $options['questions'];
        foreach ($questions as $key=>$question) {
            if($question instanceof AmpedTextareaType)
            $builder->add('question'.$key, AmpedTextareaType::class , array('label'=>$question->getText()));
            else if($question instanceof AmpedChoiceType)
            {
                    switch($question->getGroupType())
                    {
                        case 1:
                            $builder->add('question'.$key, AmpedChoiceType::class, array('label'=>$question->getText(), 'placeholder'=>'', 
                                'choices' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D'=>'D', 'F'=>'F'],
//                                'expanded' => true
                                ));
                            break;
                        case 2:
                            $builder->add('question'.$key, AmpedChoiceType::class , array('label'=>$question->getText(), 'placeholder'=>'', 
                                'choices' => array_combine(range(1, 10), range(1, 10)),
//                                'expanded' => true
                                ));
                            break;                            
                        case 3:
                            $builder->add('question'.$key, AmpedChoiceType::class , array('label'=>$question->getText(), 'placeholder'=>'', 
                                'choices' => [
                                    'Not at all' => ['1' => 1, '2' => 2, '3' => 3],
                                    'Somewhat' => ['4'=>4,'5'=>5,'6'=>6, '7'=>7],
                                    'Very' => ['8' => 8, '9'=>9, '10'=>10]
                                ],
//                                'expanded' => true
                                ));
                            break;                            
                        case 4:
                            $builder->add('question'.$key, AmpedChoiceType::class , array('label'=>$question->getText(), 'placeholder'=>'', 
                                'choices' => [
                                    'Definitely not' => ['0'=>0],
                                    'Probably not' => ['2' => 2, '3' => 3],
                                    'Maybe' => ['4' => 4, '5' => 5, '6' => 6],
                                    'Probably' => ['7' => 7, '8' => 8],
                                    'Definitely' => ['9' => 9, '10' => 10]
                                ],
//                                'expanded' => true
                                ));
                            break;                                                        
                    }                    
//                $builder->add('question'.$key, AmpedChoiceType::class , array('label'=>$question->getText(), 'placeholder'=>'', 'choices' => range(1, $question->getChoiceCount())));
            }
        }
        $builder->add('submit', SubmitType::class, ['label' => 'Submit']);
        
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'questions' => null
        ));
    }
}
