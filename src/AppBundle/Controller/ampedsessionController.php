<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
/**
 * Description of AmpedSessionAdmin
 *
 * @author sagar
 */
class ampedsessionController extends BaseAdminController {
    //put your code here
    
    public function prePersistEntity($entity) {
        
        if(null !== $this->getSelfAssessmentAcademicQuestions())
        $entity->getSelfAssessmentAcademicQuestions()->setTitle('Self Assessment: Academic');
        if (null !== $this->getSelfAssessmentSocialQuestions())
        $entity->getSelfAssessmentSocialQuestions()->setTitle('Self Assessment: Social');
        if (null !== $this->getSelfAssessmentBehaviourQuestions())
        $entity->getSelfAssessmentBehaviourQuestions()->setTitle('Self Assessment: Behaviour');
        if (null !== $this->getSelfAssessmentSelfRegQuestions())
        $entity->getSelfAssessmentSelfRegQuestions()->setTitle('Self Assessment: Self-regulation');   
        
        parent::prePersistEntity($entity);
    }
    
}
