<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * QuestionSet
 */
class QuestionSet
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

//    /**
//     * @var \stdClass
//     */
//    private $createdBy;
//
//    /**
//     * @var array
//     */
//    private $questions;

    
//    private $session;
    
    
    public function __construct() {
        $this->questions = new ArrayCollection();
    }
    
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

//    /**
//     * Set amped session
//     *
//     * @param ampedSession $session
//     *
//     * @return QuestionSet
//     */
//    public function setSession($session)
//    {
//        $this->session = $session;
//
//        return $this;
//    }
//
//    /**
//     * Get amped session
//     *
//     * @return ampedSession
//     */
//    public function getSession()
//    {
//        return $this->session;
//    }   
    
    
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return QuestionSet
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

//    /**
//     * Set createdBy
//     *
//     * @param \stdClass $createdBy
//     *
//     * @return QuestionSet
//     */
//    public function setCreatedBy($createdBy)
//    {
//        $this->createdBy = $createdBy;
//
//        return $this;
//    }
//
//    /**
//     * Get createdBy
//     *
//     * @return \stdClass
//     */
//    public function getCreatedBy()
//    {
//        return $this->createdBy;
//    }
//
//    /**
//     * Set questions
//     *
//     * @param array $questions
//     *
//     * @return QuestionSet
//     */
//    public function setQuestions($questions)
//    {
//        $this->questions = $questions;
//
//        return $this;
//    }
//
//    /**
//     * Get questions
//     *
//     * @return array
//     */
//    public function getQuestions()
//    {
//        return $this->questions;
//    }
}

