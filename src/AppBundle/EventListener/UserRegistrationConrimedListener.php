<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\LegacyUser;

/**
 * Listener responsible to import legacy user data after registration
 * (Registration in File app/config/services.yml --> )
 */
class UserRegistrationConrimedListener implements EventSubscriberInterface
{
    protected $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onUserRegistrationConfirmed',
        );
    }

    public function onUserRegistrationConfirmed(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $em = $this->entityManager;
        $repository = $em->getRepository('AppBundle:LegacyUser');
        $legacyUser = $repository->findOneByMail($user->getEmail());
        if ($legacyUser) {
            $user->setForename($legacyUser->getForename());
            $user->setSurname($legacyUser->getSurname());
            $user->setStreet($legacyUser->getAddress());
            $user->setZip($legacyUser->getZip());
            $user->setCity($legacyUser->getCity());
            $user->setCountry($legacyUser->getCountry());
            $user->setGender($legacyUser->getGender());
            $user->setDateOfBirth($legacyUser->getDateOfBirth());
            $user->setPhone($legacyUser->getPhone());
            $user->setOccupation($legacyUser->getOccupation());
            $em->persist($user);
            $em->flush();
        }
    }
}
