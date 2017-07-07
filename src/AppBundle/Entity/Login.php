<?php

namespace AppBundle\Entity;

/**
 * Login
 */
class Login
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $ts;

    /**
     * @var bool
     */
    private $status;

    private $user;

    /**
     * Set user
     *
     * @param \DateTime $user
     *
     * @return Login
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $user;
    }

    /**
     * Get user
     *
     * @return \DateTime
     */
    public function getUser()
    {
        return $this->user;
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

    /**
     * Set ts
     *
     * @param \DateTime $ts
     *
     * @return Login
     */
    public function setTs($ts)
    {
        $this->ts = $ts;

        return $this;
    }

    /**
     * Get ts
     *
     * @return \DateTime
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Login
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
}

