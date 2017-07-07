<?php

namespace AppBundle\Entity;

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

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;
    
    private $users;
    
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

    public function getUsers()
    {
        return $this->users;
    }
    /**
     * Set users
     *
     * @param integer $users
     *
     * @return Session
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

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

}

