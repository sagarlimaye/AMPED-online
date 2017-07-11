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

    /**
     * @var User
     */
    private $user;

    
    public function __construct() {
        $this->ts = new \DateTime(date('Y-m-d'));
    }


    /**
     * Set user
     *
     * @param User $user
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
     * @return User
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

