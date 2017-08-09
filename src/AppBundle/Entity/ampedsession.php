<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

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

//    /**
//     * @var string
//     */
//    private $title;
//    
//    /**
//     * @var string
//     */
//    private $description;

    /**
     * @var int
     */
    private $createdBy;

    /**
     * @var \DateTime
     */
    private $createdAt;
    
    
    private $tic;
    private $abm;
//    private $icebreakerQuestions;
//    private $miscQuestions;
    private $hasMeShield;
    private $has7Words;
    private $hasBackpack;
    private $hasTimeTravel;
    private $hasBackToBack;
    
    private $hasOrgChart;
    private $hasModules;
    private $hasGoalSheet;
    private $changeFormQuestions;
    private $selfAssessmentQuestions;
    private $MAFQuestions;
    private $pages;
    
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
        $this->icebreakerQuestions = new ArrayCollection();
        $this->miscQuestions = new ArrayCollection();
        $this->has7Words = false;
    }
    
    
        $this->hasModules = false;
        $this->hasMeShield = false;
        $this->hasOrgChart = false;
    /**
     * Set whether this session should contain goal sheet survey
     *
     * @param bool $hasGoalSheet
     *
     * @return ampedsession
     */
    public function setHasGoalSheet($hasGoalSheet)
    {
        $this->hasGoalSheet = $hasGoalSheet;

        return $this;
    }

    /**
     * Get whether this session should contain goal sheet survey
     *
     * @return bool
     */
    public function getHasGoalSheet()
    {
        return $this->hasGoalSheet;
    }

    
    /**
     * Set whether this session should contain the 7 Words icebreaker activity
     *
     * @param bool $has7Words
     *
     * @return ampedsession
     */
    public function setHas7Words($has7Words)
    {
        $this->has7Words = $has7Words;

        return $this;
    }

    /**
     * Get whether this session should contain the 7 Words icebreaker activity
     *
     * @return bool
     */
    public function getHas7Words()
    {
        return $this->has7Words;
    }

    /**
     * Set whether this session should contain the Organization chart
     *
     * @param bool $hasOrgChart
     *
     * @return ampedsession
     */
    public function setHasOrgChart($hasOrgChart)
    {
        $this->hasOrgChart = $hasOrgChart;

        return $this;
    }

    /**
     * Get whether this session should contain the Organization chart
     *
     * @return bool
     */
    public function getHasOrgChart()
    {
        return $this->hasOrgChart;
    }

    /**
     * Set whether this session should contain the Me Shield Icebreaker activity
     *
     * @param bool $hasMeShield
     *
     * @return ampedsession
     */
    public function setHasMeShield($hasMeShield)
    {
        $this->hasMeShield = $hasMeShield;

        return $this;
    }

    /**
     * Get whether this session should contain the Me Shield Icebreaker activity
     *
     * @return bool
     */
    public function getHasMeShield()
    {
        return $this->hasMeShield;
    }

    /**
     * Set whether this session should contain the Backpack scavenger hunt activity
     *
     * @param bool $hasBackpack
     *
     * @return ampedsession
     */
    public function setHasBackpack($hasBackpack)
    {
        $this->hasBackpack = $hasBackpack;

        return $this;
    }

    /**
     * Get whether this session should contain the Me Shield Icebreaker activity
     *
     * @return bool
     */
    public function getHasBackpack()
    {
        return $this->hasBackpack;
    }

     /* Set whether this session should contain the Back To Back icebreaker activity
     *
     * @param bool $hasBackToBack
     *
     * @return ampedsession
     */
    public function setHasBackToBack($hasBackToBack)
    {
        $this->hasBackToBack = $hasBackToBack;

        return $this;
    }

    /**
     * Get whether this session should contain the Me Shield Icebreaker activity
     *
     * @return bool
     */
    public function getHasBackToBack()
    {
        return $this->hasBackToBack;
    }
    
    
    
    /**
     * Set whether this session should contain the Time Traveling Icebreaker activity
     *
     * @param bool $hasTimeTravel
     *
     * @return ampedsession
     */
    public function setHasTimeTravel($hasTimeTravel)
    {
        $this->hasTimeTravel = $hasTimeTravel;

        return $this;
    }

    /**
     * Get whether this session should contain the Time Traveling Icebreaker activity
     *
     * @return bool
     */
    public function getHasTimeTravel()
    {
        return $this->hasTimeTravel;
    }
    
    /**
     * Set whether this session should contain a module seleciton
     *
     * @param integer $hasModules
     *
     * @return ampedsession
     */
    public function setHasModules($hasModules)
    {
        $this->hasModules = $hasModules;

        return $this;
    }

    /**
     * Get whether this session should contain the 7 Words icebreaker activity
     *
     * @return bool
     */
    public function getHasModules()
    {
        return $this->hasModules;
    }
    
    public function getTic()
    {
        return $this->tic;
    }
    public function setTic($tic)
    {
        $this->tic = $tic;
        return $this;
    }
    public function getAbm()
    {
        return $this->abm;
    }
    public function getChangeForm()
    {
        return $this->changeFormQuestions;
    }
    
    public function setChangeFormQuestions($changeFormQuestions)
    {
        $this->changeFormQuestions = $changeFormQuestions;
        return $this;
    }
    public function getSelfAssessmentQuestions()
    {
        return $this->selfAssessmentQuestions;
    }
    public function setSelfAssessmentQuestions($selfAssessmentQuestions)
    {
        $this->selfAssessmentQuestions = $selfAssessmentQuestions;
        return $this;
    }

    public function getMAFQuestions()
    {
        return $this->MAFQuestions;
    }
    public function setMAFQuestions($MAFQuestions)
    {
        $this->MAFQuestions = $MAFQuestions;
        return $this;
    }
    
//    public function getIcebreakerQuestions()
//    {
//        return $this->icebreakerQuestions;
//    }
//    public function setIcebreakerQuestions($icebreakerQuestions)
//    {
//        $this->icebreakerQuestions = $icebreakerQuestions;
//        return $this;
//    }
//    public function getMiscQuestions()
//    {
//        return $this->miscQuestions;
//    }
//    public function setMiscQuestions($miscQuestions)
//    {
//        $this->miscQuestions = $miscQuestions;
//        return $this;
//    }

    public function getPages()
    {
        return $this->pages;
    }
    public function setPages($pages)
    {
        $this->pages = $pages;
        return $this;
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

//    /**
//     * Set title
//     *
//     * @param string $title
//     *
//     * @return ampedsession
//     */
//    public function setTitle($title)
//    {
//        $this->title = $title;
//
//        return $this;
//    }
//
//    /**
//     * Get title
//     *
//     * @return string
//     */
//    public function getTitle()
//    {
//        return $this->title;
//    }
//
//    /**
//     * Set description
//     *
//     * @param string $description
//     *
//     * @return ampedsession
//     */
//    public function setDescription($description)
//    {
//        $this->description = $description;
//
//        return $this;
//    }
//
//    /**
//     * Get description
//     *
//     * @return string
//     */
//    public function getDescription()
//    {
//        return $this->description;
//    }
//
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
    public function hasIcebreakers()
    {
        return $this->has7Words || $this->hasMeShield || (null !== $this->abm) || (null !== $this->tic);
    }
}
