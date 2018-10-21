<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Http\Event\SwitchUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SwitchUserListener implements EventSubscriberInterface
{
    public $authChecker;
    public function __construct(AuthorizationChecker $authChecker) {
        $this->authChecker = $authChecker;
    }
    public function onSwitchUser(SwitchUserEvent $event)
    {
        if($event->getRequest()->query->get('_switch_user') != '_exit')
        {
            $user = $event->getTargetUser();

            if(!$this->authChecker->isGranted('IMPERSONATION', $user))
                throw new AccessDeniedHttpException('Impersonation failed.');
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // constant for security.switch_user
            SecurityEvents::SWITCH_USER => 'onSwitchUser',
        );
    }
}