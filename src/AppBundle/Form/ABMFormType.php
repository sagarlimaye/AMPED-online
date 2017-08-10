<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Entity\AdjectiveAmpedChoiceType;
use AppBundle\Entity\AmpedTextareaType;
/**
 * Description of TICFormType
 *
 * @author sagar
 */
class ABMFormType extends AbstractType
{
//    private $questions;
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $questions = $options['questions'];
        foreach ($questions as $question) {
            $builder->add($question, $question instanceof AdjectiveAmpedChoiceType ? AdjectiveAmpedChoiceType::class : AmpedTextareaType::class, array('label'=>$question->text));
        }
        $builder->add('Submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }
//    public function __construct($questions) {
//        $this->questions = $questions;
//    }
//    public function setQuestions($questions)
//    {
//        $this->questions = $questions;
//        return $this;
//    }
//    public function getQuestions()
//    {
//        return $this->questions;
//    }
}
