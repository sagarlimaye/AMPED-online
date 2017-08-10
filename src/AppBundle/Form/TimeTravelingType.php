<?php

namespace AppBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * SevenWordsType
 */
class TimeTravelingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//                ->add('issue', TextType::class, array('label' => 'Specify an issue or a goal: '))
//                ->add('question1', TextType::class, array('label' => 'Question about that issue: '))
//                ->add('answer1', TextType::class, array('label' => 'Your answer to the above question: '));
//        for ($i = 2; $i <= 3; $i++) {
//            $builder
//                    ->add('question' . $i, TextType::class, array('label' => 'Question for the above answer: '))
//                    ->add('answer' . $i, TextType::class, array('label' => 'Your answer to the above question: '));
//        }
        $builder->add('year', TextType::class)
                ->add('event', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class)
                ->add('Submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }
   
}

