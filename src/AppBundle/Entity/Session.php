<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * Session
 */
class Session
{
    protected $id;
    /**
     * @var int
     */
    private $ampedSession;
  
    private $student;
    private $mentor;
    
    private $icebreakerCompleted;
    private $moduleCompleted;
//    private $otherAnswers;
    
    
    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;
    
//    private $users;

//    private $activitiesCompleted;
    public function __construct()
    {
//        $this->users = new ArrayCollection();
//        $this->activitiesCompleted = new ArrayCollection();
//          $this->otherAnswers = new ArrayCollection();
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

    public function getAmpedSession()
    {
        return $this->ampedSession;
    }
    /**
     * Set amped session
     *
     * @param ampedsession $ampedsession
     *
     * @return Session
     */
    public function setAmpedSession($ampedSession)
    {
        $this->ampedSession = $ampedSession;

        return $this;
    }

//    public function getUsers()
//    {
//        return $this->users;
//    }
//    /**
//     * Set users
//     *
//     * @param integer $users
//     *
//     * @return Session
//     */
//    public function setUsers($users)
//    {
//        $this->users = $users;
//
//        return $this;
//    }
//    public function addUser($user)
//    {
//        $this->users->add($user);
//        return $this;
//    }
    
    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Session
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set student
     *
     * @param User $end
     *
     * @return Session
     */
    public function setStudent($student)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return User
     */
    public function getStudent()
    {
        return $this->student;
    } 

    /**
     * Set mentor
     *
     * @param User $mentor
     *
     * @return Session
     */
    public function setMentor($mentor)
    {
        $this->mentor = $mentor;

        return $this;
    }

    /**
     * Get mentor
     *
     * @return User
     */
    public function getMentor()
    {
        return $this->mentor;
    }    
    
    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Session
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    } 
    
    public function getIcebreakerCompleted()
    {
        return $this->icebreakerCompleted;
    }
    public function setIcebreakerCompleted($icebreakerCompleted)
    {
        $this->icebreakerCompleted = $icebreakerCompleted;
        return $this;
    }

    public function getModuleCompleted()
    {
        return $this->moduleCompleted;
    }
    public function setModuleCompleted($moduleCompleted)
    {
        $this->moduleCompleted = $moduleCompleted;
        return $this;
    }
//
//    public function addOtherAnswers($otherAnswer)
//    {
//        $key = '';
//        if($otherAnswer instanceof MAFAnswers)
//            $key = 'maf';
//        else if($otherAnswer instanceof ChangeSurveyAnswers)
//            $key = 'change';
//        else if($otherAnswer instanceof GoalSheetAnswers)
//            $key = 'goal';
//        else if($otherAnswer instanceof SelfAssessmentAnswers)
//            $key = 'self';
//        $this->otherAnswers->set($key, $otherAnswer);
//    }
//    public function removeOtherAnswers($otherAnswers)
//    {
//        $this->otherAnswers->removeElement($otherAnswers);
//    }
//    public function getAnswers()
//    {
//        return $this->otherAnswers->toArray();
//    }
//    
//    public function getMAF()
//    {
//        return $this->otherAnswers->get('maf');
//    }
//    
//    public function getChangeSurvey()
//    {
//        return $this->otherAnswers->get('change');
//    }
//
//    public function getGoalSheet()
//    {
//        return $this->otherAnswers->get('goal');
//    }
//    public function getSelfAssessment()
//    {
//        return $this->otherAnswers->get('self');
//    }
//    
//    public function completedMAF()
//    {
//        return !$this->otherAnswers->isEmpty() && $this->otherAnswers->containsKey('maf');
//    }
//    public function completedChangeSurvey()
//    {
//        return !$this->otherAnswers->isEmpty() && $this->otherAnswers->containsKey('change');
//    }    
//    public function completedGoalSheet()
//    {
//        return !$this->otherAnswers->isEmpty() && $this->otherAnswers->containsKey('goal');
//    }    
//    public function completedSelfAssessment()
//    {
//        return !$this->otherAnswers->isEmpty() && $this->otherAnswers->containsKey('self');
//    }    
}

