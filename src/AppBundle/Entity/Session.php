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
    
    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;
    
//    private $users;

    public function __construct()
    {
//        $this->users = new ArrayCollection();
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
     * Set mentee
     *
     * @param integer $mentee
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
     *
     * @return Session
     */
    public function setActivitiesCompleted($activitiesCompleted)
    {
        $this->activitiesCompleted = $activitiesCompleted;

        return $this;
    }

    /**
     * Get activitiesCompleted
     *
     * @return Activity
     */
    public function getActivitiesCompleted()
    {
        return $this->activitiesCompleted;
    } 
    
    
}

