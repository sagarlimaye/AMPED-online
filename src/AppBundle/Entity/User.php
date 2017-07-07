<?php

namespace AppBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * User
 */
class User extends BaseUser
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var int
     */
    private $sessionCount;
    /**
     * @var string
     */
    private $firstName = 'S';

    /**
     * @var string
     */
    private $lastName = 'J';

    /**
     * @var \DateTime
     */
    private $dob;

    /**
     * @var string
     */
    private $institution='UH';

    /**
     * @var \DateTime
     */
    private $joinDate;
   
    private $mentor;
    private $students;
    
    private $sessions;
    
    private $logins;
    private $sessionCreations;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function __construct() {
        $this->students = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->logins = new ArrayCollection();
        $this->sessionCreations = new ArrayCollection();
        $this->dob = new \DateTime('1994-06-11');
        $this->joinDate = new \DateTime('2017-06-30');
        
        parent::__construct();
    }
    
    
    /**
     * Get number of sessions completed
     *
     * @return int
     */
    public function getSessionCount()
    {
        return $this->sessionCount;
    }

    /**
     * Decrease number of sessions
     *
     * @return User
     */
    public function decreaseSessionCount()
    {
        $this->firstName--;
    }
        
    /**
     * Get all logins
     *
     * @return int
     */
    public function getLogins()
    {
        return $this->logins;
    }
    
        /**
     * Get session of this mentee
     *
     * @return User
     */
    public function getSessions()
    {
        $this->sessions;
    }
 
    /**
     * Set session of this mentee
     *
     * @param int $mentor
     *
     * @return User
     */
    public function setSessions($sessions)
    {
        $this->sessions = $sessions;
        
        return $this;
    }

    /**
     * Get students
     *
     * @return User
     */
    public function getStudents()
    {
        $this->students;
    }
 
    /**
     * Set students
     *
     * @param User $students
     *
     * @return User
     */
    public function setStudents($students)
    {
        $this->students = $students;
        
        return $this;
    }
    
    
    
    /**
     * Get Mentor
     *
     * @return User
     */
    public function getMentor()
    {
        $this->mentor;
    }
 
    /**
     * Set mentor
     *
     * @param int $mentor
     *
     * @return User
     */
    public function setMentor($mentor)
    {
        $this->mentor = $mentor;
        
        return $this;
    }
    
    
    
    /**
     * Increase number of sessions
     *
     * @return User
     */
    public function increaseSessionCount()
    {
        $this->firstName++;
    }
    /**
     * Set number of sessions
     *
     * @param int $sessionCount
     *
     * @return User
     */
    public function setSessionCount($sessionCount)
    {
        $this->firstName = $sessionCount;
        
        return $this;
    }
    
    /**
     * Set sessionCreations
     */
    public function setSessionCreations($sessionCreations)
    {
        $this->sessionCreations = $sessionCreations;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getSessionCreations()
    {
        return $this->sessionCreations;
    }
      
    
    
    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    
    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set dob
     *
     * @param \DateTime $dob
     *
     * @return User
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set institution
     *
     * @param string $institution
     *
     * @return User
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set joinDate
     *
     * @param \DateTime $joinDate
     *
     * @return User
     */
    public function setJoinDate($joinDate)
    {
        $this->joinDate = $joinDate;

        return $this;
    }

    /**
     * Get joinDate
     *
     * @return \DateTime
     */
    public function getJoinDate()
    {
        return $this->joinDate;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
}
