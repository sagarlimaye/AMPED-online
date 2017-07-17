<?php

namespace AppBundle\Entity;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
/**
 * AmpedChoiceType
 */
class AmpedChoiceType extends QuestionType
{
    public function getParent() {
        return ChoiceType::class;
    }
}

