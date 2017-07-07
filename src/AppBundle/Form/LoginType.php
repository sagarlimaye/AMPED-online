<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Description of LoginType
 *
 * @author sagar
 */
class LoginType extends AbstractType
{
    //put your code here
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
    }
    public function getParent() {
        return 'fos_user_login';
    }
    public function getName()
    {
        return 'app_user_login';
    }
    
    
    
    
}
