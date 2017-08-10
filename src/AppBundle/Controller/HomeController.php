<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Session;
use AppBundle\Entity\ampedsession;
use AppBundle\Form;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE') and amped.hasIcebreakers()")
     */        
    public function icebreakerAction(ampedsession $amped)
    {
        $user = $this->getUser();
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\AnswerSet');
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');        
        
        if(!$session->getIcebreakerCompleted())
        // go to the icebreaker selection page
        return $this->render('student/icebreaker_selection.html.twig', ['current' => $amped] );
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
    
    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE') and null !== amped.getMAFQuestions()")
     */    
    public function MAFAction(ampedsession $amped, Request $request)
    {
        $user = $this->getUser();
        $questions = $amped->getMAFQuestions()->getQuestions()->toArray();

        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');        
        
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\MAFAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);

        if(null === $answers)
        {
            //create MAF form
            $form = $this->createForm(Form\MAFFormType::class, null, array('questions'=> $questions,
                    'action'=> $this->generateUrl('mentorship_agreement', ['num'=>$amped->getNum()])));

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $mafanswerSet = new \AppBundle\Entity\MAFAnswers();
                $mafanswerSet->setUser($user);
                $mafanswerSet->setQuestionSet($amped->getMAFQuestions());
                $answers = $form->getData();
                $mafanswerSet->setAnswers($answers);
                $mafanswerSet->setSession($session);
                $em->persist($mafanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
            }
            return $this->render('student/mentorship_agreement.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/mentorship_agreement_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers->getAnswers())]);
    }
   
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE') and null !== amped.getTic()")
     */    
    public function TICAction(ampedsession $amped, Request $request)
    {
        $user = $this->getUser();
        $questions = $amped->getTic()->getQuestions()->toArray();
        
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\ThingsInCommonAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        
        if(null === $answers)
        {
            //create form
            $form = $this->createForm(Form\TICFormType::class, null, array('questions'=> $questions,
                    'action'=> $this->generateUrl('things_common', ['num'=>$amped->getNum()])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $ticanswerSet = new \AppBundle\Entity\ThingsInCommonAnswers();
                $ticanswerSet->setUser($user);
                $ticanswerSet->setQuestionSet($amped->getTic());
                $answers = $form->getData();
                $ticanswerSet->setAnswers($answers);
                $ticanswerSet->setSession($session);
                $session->setIcebreakerCompleted(true);
                $em->persist($ticanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/things_common_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers)]);
            }
            return $this->render('student/things_common.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/things_common_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers->getAnswers())]);
    }    
    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */    
    public function ABMAction(ampedsession $amped, Request $request)
    {
        $user = $this->getUser();
        
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        $questions = $amped->getAbm()->getQuestions()->toArray();
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\ABMAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        
        if(null === $answers)
        {
            //create form
            $form = $this->createForm(Form\ABMFormType::class, null, array('questions'=> $questions,
                    'action'=> $this->generateUrl('all_about_me', ['num'=>$amped->getNum()])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $abmanswerSet = new \AppBundle\Entity\ABMAnswers();
                $abmanswerSet->setUser($user);
                $abmanswerSet->setQuestionSet($amped->getTic());
                $answers = $form->getData();
                $abmanswerSet->setAnswers($answers);
                $abmanswerSet->setSession($session);
                $session->setIcebreakerCompleted(true);
                $em->persist($abmanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/all_about_me_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers)]);
            }
            return $this->render('student/all_about_me.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/all_about_me_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers->getAnswers())]);
    }    
    
    
    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */    
    public function ChangeFormAction(ampedsession $amped, Request $request)
    {
        $user = $this->getUser();
        $questions = $amped->getChangeFormQuestions()->getQuestions()->toArray();
        
        $session = $this->queryForStudentSession($amped, $user);
        
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\ChangeSurveyAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        
        if(null === $answers)
        {
            //create MAF form
            $form = $this->createForm(Form\ChangeSurveyType::class, null, array('questions'=> $questions,
                    'action'=> $this->generateUrl('change_survey', ['num'=>$amped->getNum()])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $changeanswerSet = new \AppBundle\Entity\ChangeSurveyAnswers();
                $changeanswerSet->setUser($user);
                $changeanswerSet->setQuestionSet($amped->getChangeFormQuestions());
                $answers = $form->getData();
                $changeanswerSet->setAnswers($answers);
                $changeanswerSet->setSession($session);
                $em->persist($changeanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/change_survey_complete.html.twig', ['questions'=>$questions, 'answers'=>$answers]);
            }
            return $this->render('student/change_survey.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/change_survey_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers->getAnswers())]);
    }

    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */        
    public function backpackAction(ampedsession $amped, Request $request)
    {   
        $user = $this->getUser();
        
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\BackpackScavengerAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\BackpackType::class, null, array('action'=> $this->generateUrl('backpack', ['num'=>$amped->getNum()])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $backpackSet = new \AppBundle\Entity\BackpackScavengerAnswers();
                $backpackSet->setUser($user);
                $answers = $form->getData();
                $backpackSet->setAnswers($answers);
                $backpackSet->setSession($session);
                $session->setIcebreakerCompleted(true);
                $em->persist($backpackSet);
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                $em->flush();
                return $this->render('student/backpack_scavenger_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/backpack_scavenger.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/backpack_scavenger_complete.html.twig', ['answers'=>$answers->getAnswers()]);
    }

    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */        
    public function sevenWordsAction(ampedsession $amped, Request $request)
    {   
        $user = $this->getUser();
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\SevenWordsAnswers');

        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\SevenWordsType::class, null, array('action'=> $this->generateUrl('seven_words', ['num'=>$amped->getNum()])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $sevenwordsSet = new \AppBundle\Entity\SevenWordsAnswers();
                $sevenwordsSet->setUser($user);
                $answers = $form->getData();
                $sevenwordsSet->setAnswers($answers);
                $sevenwordsSet->setSession($session);
                $session->setIcebreakerCompleted(true);
                $em->persist($sevenwordsSet);
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                $em->flush();
                return $this->render('student/seven_words_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/seven_words.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/seven_words_complete.html.twig', ['answers'=>$answers->getAnswers()]);
    }

    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */    
    public function meShieldAction(ampedsession $amped)
    {   
        $user = $this->getUser();
        
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        $session->setIcebreakerCompleted(true);
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->render('student/me_shield.html.twig');
    }    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */    
    public function backToBackAction(ampedsession $amped)
    {   
        $user = $this->getUser();
        
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        $session->setIcebreakerCompleted(true);
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->render('student/back_to_back.html.twig');
    }    
    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */    
    public function timeTravelAction(ampedsession $amped, Request $request)
    {   
        $user = $this->getUser();
        
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');

        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\TimeTravelingAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create form
            $form = $this->createForm(Form\TimeTravelingType::class, null, array('action'=> $this->generateUrl('time_travel', ['num'=>$amped->getNum()])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $timetravelSet = new \AppBundle\Entity\TimeTravelingAnswers();
                $timetravelSet->setUser($user);
                $answers = $form->getData();
                $timetravelSet->setAnswers($answers);
                $timetravelSet->setSession($session);
                $session->setIcebreakerCompleted(true);
                $em->persist($timetravelSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/time_travel_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/time_travel.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/time_travel_complete.html.twig', ['answers'=>$answers->getAnswers()]);
    }   

    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */    
    public function GoalSheetAction(ampedsession $amped, Request $request)
    {   
        $user = $this->getUser();
        
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\GoalSheetAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\GoalSheetType::class, null, array('action'=> $this->generateUrl('goal_sheet', ['num'=>$amped->getNum()])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $goalsheetAnswerSet = new \AppBundle\Entity\GoalSheetAnswers();
                $goalsheetAnswerSet->setUser($user);
                $answers = $form->getData();
                date_default_timezone_set('America/Chicago');
                $answers['date'] = date('l - F j, Y');
                $goalsheetAnswerSet->setAnswers($answers);
                $goalsheetAnswerSet->setSession($session);
                $em->persist($goalsheetAnswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/goal_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/goal_sheet.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/goal_complete.html.twig', ['answers'=>$answers->getAnswers()]);
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
    
    public function advanceSession($session, $user)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $session->setEnd(new \DateTime(null, new \DateTimeZone('America/Chicago')));
        $amped = $em->getRepository('AppBundle\Entity\ampedsession')
                ->findOneBy(['num' => $session->getAmpedSession()->getNum() + 1]);
        if(null !== $amped)
        {
            $newSession = new Session();
            $start = new \DateTime(null, new \DateTimeZone('America/Chicago'));
            $start->add(new \DateInterval('P3D'));
            
            $newSession->setStart($start);
            $newSession->setAmpedSession($amped);
            $newSession->setStudent($user);
            $newSession->setMentor($user->getMentor());
            
            $em->persist($newSession);
        }
        $em->flush();            
    }    
}
?>

