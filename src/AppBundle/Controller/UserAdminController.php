<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

/**
 * Description of UserController
 *
 * @author sagar
 */
class UserAdminController extends BaseAdminController 
{
    //put your code here
    protected function prePersistEntity($entity) {
        
        $usr= $this->getUser();
        
        // send password to user's email
        
        
        
        //------------------------------
        
        parent::prePersistEntity($entity);
    }
    protected function preUpdateEntity($entity) {
        
        $usr= $this->getUser();

        // send updated password to user's email
        
        
        
        //------------------------------        
        
        parent::preUpdateEntity($entity);
    }
    
    
}
