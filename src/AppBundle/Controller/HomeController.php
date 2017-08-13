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

    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE') and null !== amped.getPages()")
     */        
    public function viewAction(ampedsession $amped, $num,$pageno)
    {
        $user = $this->getUser();
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\AnswerSet');
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');        
        
        
        $pages = $amped->getPages();
        
        if($pages->containsKey($pageno))
        return $this->render('student/simple_page.html.twig', ['page'=>$pages[$pageno]]);
        else throw $this->createNotFoundException ();
    }

    
    private function resumeSession($session)
    {
        $ampedSession = $session->getAmpedSession();
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\AnswerSet');
        if (!$ampedSession->getPages()->isEmpty()) // check if there are info pages
        {
            // go to the first page
            $firstPage = $ampedSession->getPages()[0];
            return $this->render('student/simple_page.html.twig', ['page' => $firstPage]);
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
     * @Security("has_role('ROLE_PROTEGE') and type in ['academic', 'emotional', 'self-reg', 'social']")
     */    
    public function SelfAssessAction(ampedsession $amped, Request $request, $type)
    {
        $user = $this->getUser();
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\SelfAssessmentAnswers');
        
        switch($type)
        {
            case 'academic':
                $questionSet = $amped->getSelfAssessmentAcademicQuestions();
                break;
            case 'social':
                $questionSet = $amped->getSelfAssessmentSocialQuestions();
                break;
            case 'emotional':
                $questionSet = $amped->getSelfAssessmentBehaviourQuestions();
                break;                
            case 'self-reg':
                $questionSet = $amped->getSelfAssessmentSelfRegQuestions();
                break;                
        }
        
        $answers = $rep->findOneBy(['session'=>$session, 'questionSet' => $questionSet]);
        $questions = $questionSet->getQuestions()->toArray();
        
        if(null === $answers)
        {
            //create form
            $form = $this->createForm(Form\SelfAssessmentFormType::class, null, array('questions'=> $questions,
                    'action'=> $this->generateUrl('self_assess', ['num'=>$amped->getNum(), 'type' => $type])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $selfassessanswerSet = new \AppBundle\Entity\SelfAssessmentAnswers();
                $selfassessanswerSet->setUser($user);
                $selfassessanswerSet->setQuestionSet($questionSet);
                $answers = $form->getData();
                $selfassessanswerSet->setAnswers($answers);
                $selfassessanswerSet->setSession($session);
                $em->persist($selfassessanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/self_assessment_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers), 'title' => ucfirst($type)]);
            }
            return $this->render('student/self_assessment.html.twig', array('form'=>$form->createView(), 'title' => ucfirst($type)));
        }
        return $this->render('student/self_assessment_complete.html.twig', ['questions'=>$questions, 'answers'=>array_values($answers->getAnswers()), 'title' => ucfirst($type)]);
    } 
    
    private function ScrolAction(ampedsession $amped, Request $request, Session $session)
    {   
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\ScrolAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\ScrolWorksheetType::class, null, array('action'=> $this->generateUrl('module', ['num'=>$amped->getNum(), 'module' => 2])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $scrolAnswerSet = new \AppBundle\Entity\ScrolAnswers();
                $scrolAnswerSet->setUser($user);
                $answers = $form->getData();
                $scrolAnswerSet->setAnswers($answers);
                $scrolAnswerSet->setSession($session);
                $session->setModuleCompleted(2);
                $em->persist($scrolAnswerSet);
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                $em->flush();
                return $this->render('student/scrol_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/scrol_worksheet.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/scrol_complete.html.twig', ['answers'=>$answers->getAnswers()]);
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

    
    
    private function gpaChangeAction(ampedsession $amped, Request $request, Session $session)
    {   
        $user = $this->getUser();
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\GPAWorksheetAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\GPAWorksheetType::class, null, array('action'=> $this->generateUrl('module', ['num'=>$amped->getNum(), 'module' => 3])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $gpaanswerSet = new \AppBundle\Entity\GPAWorksheetAnswers();
                $gpaanswerSet->setUser($user);
                $answers = $form->getData();
                $gpaanswerSet->setAnswers($answers);
                $gpaanswerSet->setSession($session);
                $session->setModuleCompleted(3);
                $em->persist($gpaanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/gpa_change_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/gpa_change.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/gpa_change_complete.html.twig', ['answers'=>$answers->getAnswers()]);                
    }    
    
    public function motivationAction(ampedsession $amped, Request $request, Session $session)
    {   
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\MotivationWorksheetAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\MotivationWorksheetType::class, null, array('action'=> $this->generateUrl('module', ['num'=>$amped->getNum(), 'module' => 4])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $motivationanswerSet = new \AppBundle\Entity\MotivationWorksheetAnswers();
                $motivationanswerSet->setUser($user);
                $answers = $form->getData();
                $motivationanswerSet->setAnswers($answers);
                $motivationanswerSet->setSession($session);
                $session->setModuleCompleted(4);
                $em->persist($motivationanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/motivation_worksheet_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/motivation_worksheet.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/motivation_worksheet_complete.html.twig', ['answers'=>$answers->getAnswers()]);                
    }    
 
    public function feedbackAction($amped, $request, $session)
    {
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\BrainstormingWorksheetAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\BrainstormingWorksheetType::class, null, array('action'=> $this->generateUrl('module', ['num'=>$amped->getNum(), 'module' => 6])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $brainstormanswerSet = new \AppBundle\Entity\BrainstormingWorksheetAnswers();
                $brainstormanswerSet->setUser($user);
                $answers = $form->getData();
                $brainstormanswerSet->setAnswers($answers);
                $brainstormanswerSet->setSession($session);
                $session->setModuleCompleted(6);                
                $em->persist($brainstormanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/feedback_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/feedback.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/feedback_complete.html.twig', ['answers'=>$answers->getAnswers()]); 
    }

    public function relaxationAction($amped, $request, $session)
    {
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\RelaxationWorksheetAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\RelaxationWorksheet::class, null, array('action'=> $this->generateUrl('module', ['num'=>$amped->getNum(), 'module' => 7])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $relaxationanswerSet = new \AppBundle\Entity\RelaxationWorksheet();
                $relaxationanswerSet->setUser($user);
                $answers = $form->getData();
                $relaxationanswerSet->setAnswers($answers);
                $relaxationanswerSet->setSession($session);
                $session->setModuleCompleted(7);                
                $em->persist($relaxationanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/relaxation_worksheet_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/relaxation_worksheet.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/relaxation_worksheet_complete.html.twig', ['answers'=>$answers->getAnswers()]); 
    }

    public function conflictAction($amped, $request, $session)
    {
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\ConflictResAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\ConflictResType::class, null, array('action'=> $this->generateUrl('module', ['num'=>$amped->getNum(), 'module' => 11])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $conflictanswerSet = new \AppBundle\Entity\ConflictResAnswers();
                $conflictanswerSet->setUser($user);
                $answers = $form->getData();
                $conflictanswerSet->setAnswers($answers);
                $conflictanswerSet->setSession($session);
                $session->setModuleCompleted(11);                
                $em->persist($conflictanswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/conflict_resolution_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/conflict_resolution.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/conflict_resolution_complete.html.twig', ['answers'=>$answers->getAnswers()]); 
    }    

    public function noteTakeAction($amped, $request, $session)
    {
        $user = $this->getUser();
        // check if already completed
        $rep = $this->getDoctrine()->getRepository('AppBundle\Entity\NoteTakingAnswers');
        $answers = $rep->findOneBy(['session'=>$session]);
        if(null === $answers)
        {
            //create goal sheet form
            $form = $this->createForm(Form\ActiveNoteType::class, null, array('action'=> $this->generateUrl('module', ['num'=>$amped->getNum(), 'module' => 11])));
                
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $em = $this->getDoctrine()->getEntityManager();
                $notetakinganswerSet = new \AppBundle\Entity\NoteTakingAnswers();
                $notetakinganswerSet->setUser($user);
                $answers = $form->getData();
                $notetakinganswerSet->setAnswers($answers);
                $notetakinganswerSet->setSession($session);
                $notetakinganswerSet->setUser($user);
                $session->setModuleCompleted(13);                
                $em->persist($notetakinganswerSet);
                $em->flush();
                if($this->checkIfSessionComplete($session))
                {
                    $this->advanceSession($session, $user);
                    return $this->render ('student/session_complete.html.twig');
                }
                return $this->render('student/active_note_worksheet_complete.html.twig', ['answers'=>$answers]);
            }
            return $this->render('student/active_note_worksheet.html.twig', array('form'=>$form->createView()));
        }
        return $this->render('student/active_note_worksheet_complete.html.twig', ['answers'=>$answers->getAnswers()]); 
    }    
      
    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE')")
     */    
    public function moduleAction(ampedsession $amped, Request $request, $module)
    {   
        $user = $this->getUser();
        
        $session = $this->queryForStudentSession($amped, $user);

        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        switch($module)
        {
            case 2:        
                return $this->ScrolAction($amped, $request, $session);
                break;
            case 3:
                return $this->gpaChangeAction($amped, $request, $session);
                break;
            case 4:
                return $this->motivationAction($amped, $request, $session);
                break;
            case 5:
                $session->setModuleCompleted(5);
                $this->getDoctrine()->getEntityManager()->flush();                
                return $this->render('student/module5.html.twig');
                break;
            case 6:
                return $this->feedbackAction($amped, $request, $session);
                break;
            case 7:
                return $this->relaxationAction($amped, $request, $session);
                break;
            case 9:
                $session->setModuleCompleted(9);
                $this->getDoctrine()->getEntityManager()->flush();                
                return $this->render('student/module9.html.twig');
                break;
            case 10:
                $session->setModuleCompleted(10);
                $this->getDoctrine()->getEntityManager()->flush();
                return $this->render('student/module10.html.twig');
                break;
            case 11:
                return $this->conflictAction($amped, $request, $session);
                break;
            case 12:
                $session->setModuleCompleted(12);
                $this->getDoctrine()->getEntityManager()->flush();
                return $this->render('student/module12.html.twig');
                break;
            case 13:
                return $this->noteTakeAction($amped, $request, $session);
                break;
            default:
                throw $this->createNotFoundException('We\'re sorry, this module is currently unavailable. Please go back and select a different one.');
                break;
        }        
    }    
    
    /**
     * @ParamConverter("num", class="AppBundle\Entity\ampedsession", options={"id" = "num"})
     * @Security("has_role('ROLE_PROTEGE') and slug in ['select', 'academic', 'emotional', 'self-regulation', 'social']")
     */    
    public function moduleSelectAction(ampedsession $amped, $slug)
    {
        if($slug == 'select')
            return $this->render('student/module_categories.html.twig', ['amped' => $amped]);
        $user = $this->getUser();
        $session = $this->queryForStudentSession($amped, $user);
        if(null === $session || !$this->isAllowedToStart($session))
            return $this->redirectToRoute ('index_list');
        
        $allModules = [
            'academic' => [2,4,5,6,9,13,12],
            'emotional' => [1, 4,7,10,11,3],
            'self-regulation' => [1, 3,4,7,11,12],
            'social' => [3,5,10,1,4]
        ];
        $moduleNames = [
            1 => 'Learning your ABC\'s',
            2 => 'Expository Reading',
            3 => 'Changing your GPA',
            4 => 'Motivation',
            5 => 'Planning for the future',
            6 => 'Feedback',
            7 => 'Relaxation',
            8 => 'Technology and Self',
            9 => 'Study Skills',
            10 => 'Coping with stress',
            11 => 'Conflict Resolution',
            12 => 'Time Management',
            13 => 'Active Note Taking'
        ];
        $completed = $this->getDoctrine()->getRepository('AppBundle\Entity\Session')->getModulesCompleted($user);
        
        $category = array_diff($allModules[$slug], $completed);
        
        return $this->render('student/module_selection.html.twig', ['amped' => $amped, 'category_name'=> ucfirst($slug), 'category'=>$category, 'modules' => $moduleNames]);
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

