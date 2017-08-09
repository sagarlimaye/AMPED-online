<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();
        if($user->hasRole('ROLE_PROTEGE'))
        {
            $sessions = $this->queryForAllStudentSessions($user);
            
            // check if never started a session
            if(empty($sessions))
                // never started a session, go to overview page
                return $this->render('student/start.html.twig');
            else
            {
                // find out what the last session was
                $currentAmped = end($sessions);
                
                // if the start date is <= current and the session isn't complete, resume the session
                if(null === $currentAmped->getEnd() && $this->isAllowedToStart($currentAmped))
                return $this->resumeSession($currentAmped);
                else return $this->render('student/session_complete.html.twig');
            }
        }
    }
}

    private function resumeSession($session)
    {
        $ampedSession = $session->getAmpedSession();
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\AnswerSet');
        if (!$ampedSession->getPages()->isEmpty()) // check if there are info pages
        {
            // go to the first page
            $firstPage = $ampedSession->getPages()[0];
            return $this->render('simple_page.html.twig', ['page' => $firstPage]);
        }
        // check if MAF form exists
        if (null !== $ampedSession->getMAFQuestions())
            return $this->forward('AppBundle:Home:MAF', ['num' => $ampedSession->getNum()]);
        
        // check if there are any icebreakers
        if($ampedSession->hasIcebreakers())
        {
            // check if not already completed
            if(!$session->getIcebreakerCompleted())
            // go to the icebreaker selection page
            return $this->render('student/icebreaker_selection.html.twig', ['current' => $ampedSession] );
        }
        if($ampedSession->getHasModules())
        {
            // check if not already completed
            if(null === $session->getModuleCompleted())
            return $this->forward('AppBundle:Home:moduleSelect', ['num' => $ampedSession->getNum(), 'slug' => 'select']);
        }
        if($this->checkIfSessionComplete($session))
            return $this->render ('student/session_complete.html.twig');
        // go to the session overview page
        else return $this->render('student/session_incomplete.html.twig');
    }
    
    /**
     * @Security("has_role('ROLE_PROTEGE')")
     */
    public function startAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        $ampedFirst = $em->getRepository('AppBundle\Entity\ampedsession')
                ->findOneBy(['num' => 1]);
        $session = new Session();
        $session->setStart(new \DateTime(null, new \DateTimeZone('America/Chicago')));
        $session->setAmpedSession($ampedFirst);
        $session->setStudent($user);
        $session->setMentor($user->getMentor());
        $em->persist($session);
        $em->flush();
        
        return $this->redirectToRoute('index_list');
    }
    
    
    public function isAllowedToStart($session)
    {
        return $session->getStart() <= new \DateTime(null, new \DateTimeZone('America/Chicago'));
    }
    
    private function queryForAllStudentSessions($user)
    {
        return $this->getDoctrine()->getRepository(Session::class)->getAllStudentSessions($user);
    }    
    
    private function queryForStudentSession($amped, $user)
    {
        return $this->getDoctrine()->getRepository(Session::class)->getStudentSessionByAmped($user, $amped);
    }
    public function checkIfSessionComplete($session)
    {
        $amped = $session->getAmpedSession();
        $complete = true;
        $em = $this->getDoctrine()->getEntityManager();
        if($amped->hasIcebreakers())
            if(null === $em->getRepository('AppBundle\Entity\AnswerSet')->getIcebreakerAnswers($session) && !$session->getIcebreakerCompleted())
                $complete = false;
        if($amped->getHasModules())
            if(null === $em->getRepository('AppBundle\Entity\AnswerSet')->getModuleAnswers($session) && (null === $session->getModuleCompleted()))
                $complete = false;
        if(null !== $amped->getMAFQuestions())////
        {
            if(null === $em->getRepository('AppBundle\Entity\MAFAnswers')->findOneBy(['session'=>$session]))
                $complete = false;
        }
        if($amped->getHasGoalSheet())
        {
            if(null === $em->getRepository('AppBundle\Entity\GoalSheetAnswers')->findOneBy(['session'=>$session]))
                $complete = false;
        }
        if(null !== $amped->getChangeFormQuestions())
        {
            if(null === $em->getRepository('AppBundle\Entity\ChangeSurveyAnswers')->findOneBy(['session'=>$session]))
                $complete = false;
        }
        if($amped->hasSelfAssessment())
        {
            if(null === $em->getRepository('AppBundle\Entity\SelfAssessmentAnswers')->findOneBy(['session'=>$session, 'questionSet' => $amped->getSelfAssessmentBehaviourQuestions()]))
                $complete = false;
            if(null === $em->getRepository('AppBundle\Entity\SelfAssessmentAnswers')->findOneBy(['session'=>$session, 'questionSet' => $amped->getSelfAssessmentSocialQuestions()]))
                $complete = false;
            if(null === $em->getRepository('AppBundle\Entity\SelfAssessmentAnswers')->findOneBy(['session'=>$session, 'questionSet' => $amped->getSelfAssessmentAcademicQuestions()]))
                $complete = false;
            if(null === $em->getRepository('AppBundle\Entity\SelfAssessmentAnswers')->findOneBy(['session'=>$session, 'questionSet' => $amped->getSelfAssessmentSelfRegQuestions()]))
                $complete = false;            
        }
        return $complete;
    }

