<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
/**
 * Description of AmpedSessionAdmin
 *
 * @author sagar
 */
class ampedsessionController extends BaseAdminController {
    //put your code here
    
        
        parent::prePersistEntity($entity);
    }
    
}
