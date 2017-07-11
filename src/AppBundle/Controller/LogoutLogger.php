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
/**
 * Description of LogoutLogger
 *
 * @author sagar
 */
class LogoutLogger extends Controller implements LogoutSuccessHandlerInterface
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
}
