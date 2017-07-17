<?php

namespace AppBundle\Entity;

/**
 * AdjectiveAmpedChoiceType
 */
class AdjectiveAmpedChoiceType extends AmpedChoiceType
{
    private $adj;
    
    public function getAdj()
    {
        return $this->adj;
    }
    public function setAdj($adj)
    {
        $this->adj = $adj;
        return $this;
    }
}

