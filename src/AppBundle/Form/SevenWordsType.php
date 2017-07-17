<?php

namespace AppBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
/**
 * SevenWordsType
 */
class SevenWordsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('issue', TextType::class, array('label' => 'Specify an issue or a goal: '))
                ->add('question1', TextType::class, array('label' => 'Question about that issue: '));
    }
}

