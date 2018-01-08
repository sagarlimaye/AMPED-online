<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
/**
 * Description of AdminController
 *
 * @author sagar
 */
class AdminController extends BaseAdminController {
    //put your code here
    protected function prePersistEntity($entity)
    {
        $usr= $this->getUser();
        
        if (method_exists($entity, 'setCreatedBy'))
        {
            $entity->setCreatedBy($usr);
        }       
        parent::prePersistEntity($entity);
    }
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function prePersistUserEntity($user)
    {
        $mailer = $this->get('swiftmailer.mailer');
        
        $message = (new \Swift_Message('UH AMPED: Welcome!'))
        ->setFrom('sagarl3232@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
            $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'password_new.txt.twig',
                array('name' => $user->getName(), 'email' => $user->getEmail(), 'uname' => $user->getUserName(), 'password' => $user->getPlainPassword(), 'dob' => $user->getDob(), 'mentor' => $user->getMentor())
            ), 'text/plain'
        )
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'Emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
    ;

        $mailer->send($message);
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }
    
    public function preUpdateUserEntity($user)
    {
        $mailer = $this->get('swiftmailer.mailer');
        $message = (new \Swift_Message('UH AMPED: Changes to your account'))
        ->setFrom('sagarl3232@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
            $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                'account_update.txt.twig',
                array('name' => $user->getName(), 'email' => $user->getEmail(), 'dob' => $user->getDob(), 'uname' => $user->getUserName(), 'password' => $user->getPlainPassword(), 'mentor' => $user->getMentor())
            ), 'text/plain'
        )
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'Emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
    ;

        $mailer->send($message);        
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }
}
