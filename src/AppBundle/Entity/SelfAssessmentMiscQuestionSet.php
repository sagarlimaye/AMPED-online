<?php

namespace AppBundle\Entity;

/**
 * SelfAssessmentMiscQuestionSet
 */
class SelfAssessmentMiscQuestionSet extends QuestionSet
{

    private $title;
    
    public function __construct() {
        $this->title = 'Self-assessment sheet';
        parent::__construct();
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
}

