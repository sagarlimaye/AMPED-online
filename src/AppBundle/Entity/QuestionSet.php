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

    /**
     * @var \stdClass
     */
    private $createdBy;
//
    /**
     * @var array
     */
   private $questions;
   
   
   private $name;
    
//    private $session;
    
    
   public function __construct() {
       $this->questions = new ArrayCollection();
       $this->createdAt = new \DateTime(null, new \DateTimeZone('America/Chicago'));
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

    /**
     * Set createdBy
     *
     * @param User $createdBy
     *
     * @return QuestionSet
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
//
//    /**
//     * Add question
//     *
//     * @param AppBundle\Entity\QuestionType $questions
//     *
//     * 
//     */
//    public function addQuestion($question)
//    {
//        $this->questions->add($question);
//    }
//
//    /**
//     * Remove question
//     *
//     * @param array $questions
//     *
//     * 
//     */
//    public function removeQuestion($question)
//    {
//        $this->questions->removeElement($question);
//    }

    public function setQuestions($questions)
    {
        $this->questions = $questions;
        return $questions;
    }

    
    /**
     * Get all questions
     *
     * @return array
     */
    public function getQuestions()
    {
        return $this->questions;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function __toString() {
        return $this->getName();
    }
}

