<?php

namespace AppBundle\Entity;
use Symfony\Component\Form\AbstractType;

/**
 * QuestionType
 */
class QuestionType extends AbstractType
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $text;


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
     * Set text
     *
     * @param string $text
     *
     * @return QuestionType
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
    public function __toString() {
        return $this->text;
        
    }
    
}

