<?php

namespace AppBundle\Entity;

/**
 * AnswerSet
 */
class AnswerSet
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $answers;

    private $user;
    
    private $session;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSession()
    {
        return $this->session;
    }
    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }
    

    
    public function getUser()
    {
        return $this->user;
    }
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
    
    
    /**
     * Set answers
     *
     * @param array $answers
     *
     * @return AnswerSet
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Get answers
     *
     * @return array
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}

