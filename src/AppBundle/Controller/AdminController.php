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
    
}
