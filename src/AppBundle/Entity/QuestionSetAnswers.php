<?php

namespace AppBundle\Entity;

/**
 * QuestionSetAnswers
 */
class QuestionSetAnswers extends AnswerSet
{
    private $questionSet;
    
    public function getQuestionSet()
    {
        return $this->questionSet;
    }
    public function setQuestionSet($questionSet)
    {
        $this->questionSet = $questionSet;
        return $this;
    }
            
            
}

