<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;

class Authorization
{
    const VALUE = 'Value';
    const MESSAGE = 'Message';

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    protected $tokenStorage;
    protected $authorizationChecker;

    /**
     * @var AppBundle\Entity\User
     */
    protected $user;

    public function __construct(
        EntityManager $em,
        TokenStorage $ts,
        AuthorizationChecker $autch
    )
    {
        $this->entityManager = $em;
        $this->securityContext = $ts;
        $this->authorizationChecker = $autch;
        $this->user = $ts->getToken()->getUser();
    }
    private function isGranted($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    public function mayDelete(Event $event)
    {
        $returnValue = array();
        // check event locked:
        if($event->getLocked()==1){
            $returnValue[Authorization::VALUE] = false;
            $returnValue[Authorization::MESSAGE] = 'Event "'.$event.'" ist gesperrt und kann nicht gelöscht werden.';
            return $returnValue;
        }

        foreach ($event->getDepartments() as $department ) {
            $commitments = $department->getCommitments();
            if($commitments && $commitments->count()){
                $returnValue[Authorization::VALUE] = false;
                $returnValue[Authorization::MESSAGE] = 'Event "'.$event.'" hat bereits Hölfer und kann nicht mehr gelöscht werden.';
                return $returnValue;
            }
        }

        //$user = $securityContext->getToken()->getUser();
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            $returnValue[Authorization::VALUE] = false;
            $returnValue[Authorization::MESSAGE] = 'Nur Administratoren dürfen Events löschen.';
            return $returnValue;
        }

        $returnValue[Authorization::VALUE] = true;
        $returnValue[Authorization::MESSAGE] = 'OK';
        return $returnValue;
    }

    /**
     * Tells if current logged in user is member of the association.
     * @return boolean true, if the user is member of the association.
     */
    public function isAssociationMember()
    {
        return $this->user->getIsAssociationMember();
    }
}

?>
