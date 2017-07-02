<?php

namespace AppBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
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
    private $entityManager;
    private $router;

    public function __construct(EntityManager $em, UrlGeneratorInterface $router)
    {
        $this->entityManager = $em;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_CONFIRMED => 'onUserRegistrationConfirmed',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onUserRegistrationInitialize',
        );
    }

    public function onUserRegistrationInitialize(GetResponseUserEvent $event)
    {
        $em = $this->entityManager;
        $repository = $em->getRepository('AppBundle:Setting');
        $settings = $repository->findAll()[0];

        if (!$settings->getCanRegister()) {
            $url = $this->router->generate('registration_denied');
            //$url = $request->router->generate('registration_denied');
            $response = new RedirectResponse($url);
            $event->setResponse(new RedirectResponse($url));
        }
    }

    public function onUserRegistrationConfirmed(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $em = $this->entityManager;
        $repository = $em->getRepository('AppBundle:LegacyUser');
        $legacyUser = $repository->findOneByMail(strtolower($user->getEmail()));
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
            $user->setIsRegular(true);
            $em->persist($user);
            $em->flush();
        }
    }
}
