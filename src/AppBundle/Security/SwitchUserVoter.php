<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface; 
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Description of SwitchUserVoter
 *
 * @author sagar
 */
class SwitchUserVoter extends Voter {
    //put your code here
    const prevAdmin = 'IMPERSONATION';

    private $doctrine, $authChecker;

    public function __construct(AccessDecisionManagerInterface $decisionManager, Registry $doctrine, AuthorizationChecker $authChecker)
    {
        $this->doctrine = $doctrine;
        $this->authChecker = $authChecker;        
    }

    protected function supports($attribute, $subject)
    {

        // if the attribute isn't one we support, return false
        if ($attribute != self::prevAdmin) {
            return false;
        }
        if (!$subject instanceof User) {
            return false;
        }
        
        return true;
    }
    
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $impersonatorUser = $token->getUser();

        if (!$impersonatorUser instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        
        $user = $subject;
        
        if($impersonatorUser == null)
            return false;

        if($user->getMentor() == null || ($user->getMentor()->getId() != $impersonatorUser->getId() && !$impersonatorUser->isSuperAdmin()))
            return false;

        return true;
    }


}
