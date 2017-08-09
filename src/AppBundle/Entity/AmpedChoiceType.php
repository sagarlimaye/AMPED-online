<?php

namespace AppBundle\Entity;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
/**
 * AmpedChoiceType
 */
class AmpedChoiceType extends QuestionType
{
    private $groupType;
    private $choiceCount;
    
    public function getParent() {
        return ChoiceType::class;
    }
        
    
    public function getGroupType() {
        return $this->groupType;
    }
    public function setGroupType($groupType) {
        $this->groupType = $groupType;
        return $this;
    }    
    public function getBlockPrefix()
    {
        return 'ampedchoicetype';
    }
}

