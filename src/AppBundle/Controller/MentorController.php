<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Description of MentorController
 *
 * @author sagar
 */
class MentorController extends Controller {
    //put your code here

    public function reviewSessionsAction()
    {
        $user = $this->getUser();
        // retrieve all students
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\User');
        $students = $rep->findBy(['mentor'=>$user]);
        
        // generate urls
        $sessions = [];
        
        foreach($students as $student)
            $sessions[$student->getName()] = $this->generateUrl('index_list', ['_switch_user'=>$student->getUsernameCanonical()]);
        
        // render
        return $this->render('mentor/sessions.html.twig', ['sessions'=>$sessions]);
    }
}
