<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * ampedsession
 */
class ampedsession
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $num;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $createdBy;

    /**
     * @var \DateTime
     */
    private $createdAt;
    
    /**
     * @var string
     */
    private $description;
    
    private $sessions;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime(null, new \DateTimeZone('America/Chicago'));
        $this->sessions = new ArrayCollection();
    }
    
    
    /**
     * Set sessions
     *
     * @param integer $sessions
     *
     * @return ampedsession
     */
    public function setSessions($sessions)
    {
        $this->sessions = $sessions;

        return $this;
    }

    /**
     * Get sessions
     *
     * @return int
     */
    public function getSessions()
    {
        return $this->sessions;
    }    
    
    
    /**
     * Set num
     *
     * @param integer $num
     *
     * @return ampedsession
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return int
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ampedsession
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdBy
     *
     * @param User $createdBy
     *
     * @return ampedsession
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ampedsession
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ampedsession
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
    public function __toString() {
        return $this->num;
    }
}
