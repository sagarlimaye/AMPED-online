<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AppBundle\Menu;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity;
/**
 * Description of Builder
 *
 * @author sagar
 */
class Builder implements \Symfony\Component\DependencyInjection\ContainerAwareInterface 
{
    //put your code here
    use ContainerAwareTrait;
    
    public function completedMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine')->getManager();
        $token = $this->container->get('security.token_storage')->getToken();
        $user = $token->getUser();
        $menu = $factory->createItem('root', ['attributes' => ['class' => 'nav nav-sidebar']]);        
        
        $sessions = $em->getRepository('AppBundle\Entity\Session')->getAllStudentSessions($user);
        $completedSessions = array_slice($sessions, 0, count($sessions) - 1);
        
        foreach($completedSessions as $session)
        {
            $amped = $session->getAmpedSession();
            $sessionMenu = 'Session '.$amped->getNum();
            $menu->addChild($sessionMenu);
            
            if(null !== $amped->getPages())
            {
                $pages = $amped->getPages();
                for($i=0;$i<$pages->count();$i++)
                {
                    $menu[$sessionMenu]->addChild($page->getTitle(), [
                        'route' => 'simple_page',
                        'routeParameters' => ['num' => $amped->getNum(), 'pageno' => $i]
                    ]);
                }
            }
            if(null !== $amped->getMAFQuestions())
            {
                $menu[$sessionMenu]->addChild('Mentorship agreement form', [
                    'route' => 'mentorship_agreement',
                    'routeParameters' => ['num' => $amped->getNum()]
                ]);
            }
            if($amped->hasIcebreakers())
            {
                $icebreaker = $em->getRepository('AppBundle\Entity\AnswerSet')->getIcebreakerAnswers($session);
                $icebreaker_text = ''; $route = ''; 
                if($icebreaker instanceof Entity\MeShieldAnswers)
                {
                    $icebreaker_text = 'Me Shield';
                    $route = 'me_shield';
                }
                else if($icebreaker instanceof Entity\ThingsInCommonAnswers)
                {
                    $icebreaker_text = 'Things in Common';
                    $route = 'things_common';
                }
                else if($icebreaker instanceof Entity\ABMAnswers)
                {
                    $icebreaker_text = 'All about me';
                    $route = 'all_about_me';
                }                
                else if($icebreaker instanceof Entity\SevenWordsAnswers)
                {
                    $icebreaker_text = 'Seven Words';
                    $route = 'seven_words';
                }
                else if($icebreaker instanceof Entity\BackpackScavengerAnswers)
                {
                    $icebreaker_text = 'Backpack scavenger hunt';
                    $route = 'backpack';
                }
                else if($icebreaker instanceof Entity\TimeTravelingAnswers)
                {
                    $icebreaker_text = 'Time traveling';
                    $route = 'time_travel';
                }
                if($icebreaker_text != '' && $route != '')
                $menu[$sessionMenu]->addChild($icebreaker_text, ['route' => $route, 'routeParameters' => ['num' => $amped->getNum()]]);                
            }
            if($amped->getHasModules())
            {
                $menu[$sessionMenu]->addChild('Skill Building', ['route' => 'module', 'routeParameters' => ['num' => $amped->getNum(), 'module'=>$session->getModuleCompleted()]]);            
            }
            if($amped->getHasGoalSheet())
            {
                $menu[$sessionMenu]->addChild('Goal sheet', [
                   'route' => 'goal_sheet',
                   'routeParameters' => ['num' => $amped->getNum()]
                ]);
            }
            if($amped->hasSelfAssessment())
            {
                $menu[$sessionMenu]->addChild('Self-assessment - Academic', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'academic']
                ]);
                $menu[$sessionMenu]->addChild('Self-assessment- Self-Regulation', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'self-reg']
                ]);
                $menu[$sessionMenu]->addChild('Self-assessment - Social', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'social']
                ]);
                $menu[$sessionMenu]->addChild('Self-assessment - Emotional', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'emotional']
                ]);                
            }
            if(null !== $amped->getChangeFormQuestions())
            {
                $menu[$sessionMenu]->addChild('Change survey', [
                    'route' => 'change_survey',
                    'routeParameters' => ['num' => $amped->getNum()]
                ]);
            }   
        }

        return $menu;
    }
    public function mainMenu(FactoryInterface $factory, array $options) 
    {
        $em = $this->container->get('doctrine')->getManager();
        $token = $this->container->get('security.token_storage')->getToken();
        $user = $token->getUser();
        
        $menu = $factory->createItem('root', ['attributes' => ['class' => 'nav nav-sidebar']]);        
        $sessions = $em->getRepository('AppBundle\Entity\Session')->getAllStudentSessions($user);
        $currentSession = end($sessions);
        if($currentSession->getStart() >= new \DateTime(null, new \DateTimeZone('America/Chicago')))
            return $menu;
        $amped = $currentSession->getAmpedSession();
        $sessionMenu = 'Session '.$amped->getNum();
        
        $menu->addChild($sessionMenu);

            if(null !== $amped->getPages())
            {
                $pages = $amped->getPages();
                for($i=0;$i<$pages->count();$i++)
                {
                    $menu[$sessionMenu]->addChild($page->getTitle(), [
                        'route' => 'simple_page',
                        'routeParameters' => ['num' => $amped->getNum(), 'pageno' => $i]
                    ]);
                }
            }
            if(null !== $amped->getMAFQuestions())
            {
                $menu[$sessionMenu]->addChild('Mentorship agreement form', [
                    'route' => 'mentorship_agreement',
                    'routeParameters' => ['num' => $amped->getNum()]
                ]);
            }
            if($amped->hasIcebreakers())
            {
                if($currentSession->getIcebreakerCompleted())
                {
                    $icebreaker = $em->getRepository('AppBundle\Entity\AnswerSet')->getIcebreakerAnswers($currentSession);
                    if(null !== $icebreaker)
                    {
                        $icebreaker_text = ''; $route = ''; 
                        if($icebreaker instanceof Entity\MeShieldAnswers)
                        {
                            $icebreaker_text = 'Me Shield';
                            $route = 'me_shield';
                        }
                        else if($icebreaker instanceof Entity\ThingsInCommonAnswers)
                        {
                            $icebreaker_text = 'Things in Common';
                            $route = 'things_common';
                        }
                        else if($icebreaker instanceof Entity\ABMAnswers)
                        {
                            $icebreaker_text = 'All about me';
                            $route = 'all_about_me';
                        }                
                        else if($icebreaker instanceof Entity\SevenWordsAnswers)
                        {
                            $icebreaker_text = 'Seven Words';
                            $route = 'seven_words';
                        }
                        else if($icebreaker instanceof Entity\BackpackScavengerAnswers)
                        {
                            $icebreaker_text = 'Backpack scavenger hunt';
                            $route = 'backpack';
                        }
                        else if($icebreaker instanceof Entity\TimeTravelingAnswers)
                        {
                            $icebreaker_text = 'Time traveling';
                            $route = 'time_travel';
                        }
                        $menu[$sessionMenu]->addChild($icebreaker_text, ['route' => $route, 'routeParameters' => ['num' => $amped->getNum()]]);
                    }
                }
                else
                {
                    $menu[$sessionMenu]->addChild('Fun activity', ['route' => 'icebreaker_select', 'routeParameters' => ['num' => $amped->getNum()]]);
                }
            }
            if($amped->getHasModules())
            {
                if(null === $currentSession->getModuleCompleted())
                {
                    $menu[$sessionMenu]->addChild('Learn a new skill', ['route' => 'module_select', 'routeParameters' => ['num' => $amped->getNum(), 'slug' => 'select']]);
                    $menu[$sessionMenu]['Learn a new skill']->setLinkAttributes(['class' => 'new-module', 'data-session' => $amped->getNum()]);
                }          
                else
                {
                    $menu[$sessionMenu]->addChild('Skill Building', ['route' => 'module', 'routeParameters' => ['num' => $amped->getNum(), 'module'=>$currentSession->getModuleCompleted()]]);                    
                }
            }
            if($amped->getHasGoalSheet())
            {
                $menu[$sessionMenu]->addChild('Goal sheet', [
                   'route' => 'goal_sheet',
                   'routeParameters' => ['num' => $amped->getNum()]
                ]);
            }
            if($amped->hasSelfAssessment())
            {
                $menu[$sessionMenu]->addChild('Self-assessment - Academic', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'academic']
                ]);
                $menu[$sessionMenu]->addChild('Self-assessment- Self-Regulation', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'self-reg']
                ]);
                $menu[$sessionMenu]->addChild('Self-assessment - Social', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'social']
                ]);
                $menu[$sessionMenu]->addChild('Self-assessment - Emotional', [
                    'route' => 'self_assess',
                    'routeParameters' => ['num' => $amped->getNum(), 'type' => 'emotional']
                ]);                
            }
            if(null !== $amped->getChangeFormQuestions())
            {
                $menu[$sessionMenu]->addChild('Change survey', [
                    'route' => 'change_survey',
                    'routeParameters' => ['num' => $amped->getNum()]
                ]);
            }

        return $menu;        
        
    }
}
