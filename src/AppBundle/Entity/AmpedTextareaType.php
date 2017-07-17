<?php

namespace AppBundle\Entity;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
/**
 * AmpedTextareaType
 */
class AmpedTextareaType extends QuestionType
{
    public function getParent() {
        return TextareaType::class;
    }

}

