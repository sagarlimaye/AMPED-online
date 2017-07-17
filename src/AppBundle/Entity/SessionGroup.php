<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * SessionGroup
 */
class SessionGroup
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
     * @var string
     */
    private $name;

    private $ampedSessions;
    
    public function __construct() {
        $this->ampedSessions = new ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SessionGroup
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
     * Set name
     *
     * @param string $name
     *
     * @return SessionGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set all sessions of this group
     *
     * @param ArrayCollection $ampedSessions
     *
     * @return SessionGroup
     */
    public function setampedSessions($ampedSessions)
    {
        $this->ampedSessions = $ampedSessions;

        return $this;
    }

    /**
     * Get all sessions of this group
     *
     * @return ArrayCollection
     */
    public function getampedSessions()
    {
        return $this->ampedSessions;
    }      

    
}

