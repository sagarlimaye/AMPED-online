<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use AppBundle\Entity\Login;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
/**
 * Description of LogoutLogger
 *
 * @author sagar
 */
class Logger extends Controller implements LogoutSuccessHandlerInterface, EventSubscriberInterface
{
    
    //put your code here
    public function onLogoutSuccess(Request $request) {
        
        $attempt = new Login();
        $attempt->setUser($this->getUser());
        $attempt->setStatus(0);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($attempt);
        $em->flush();
        
        return $this->redirect('login');
    }
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
        SecurityEvents::INTERACTIVE_LOGIN => array(
               array('onFirstLogin', 0)
           )
        );
    }
    public function onFirstLogin()
    {
        $attempt = new Login();
        $attempt->setUser($this->getUser());
        $attempt->setStatus(1);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($attempt);
        $em->flush();       
    }
    
}
