<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\Authorization;
use AppBundle\Entity\Event;

class EventService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var AppBundle\Service\Authorization
     */
    protected $authorization;
    /**
     * The repository for the Event entity
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repo;

    public function __construct(
        Authorization $auth,
        EntityManager $em
    )
    {
        $this->authorization = $auth;
        $this->entityManager = $em;
        $this->repo = $em->getRepository('AppBundle:Event');
    }

    /**
     * Gets all the upcoming events which the current user may see,
     * ordered ascending (next event on top).
     * If the event is on the current date, it counts as 'upcoming'.
     * @return AppBundle\Entity\Event[] returns an array of Event objects.
     */
    public function getUpcoming()
    {
        // user.isMember = 1 --> may see event.forMembers = 0/1
        // user.isMember = 0 --> may only see event.forMembers = 0
        // hence event.forMembers <= user.isMember
        $query = $this->repo->createQueryBuilder('e')
                ->where('e.date >= :today AND e.isForAssociationMembers <= :userIsMember')
                ->setParameters(array(
                    'today' => new \DateTime("now"),
                    'userIsMember' => $this->authorization->isAssociationMember()
                ))
                ->orderBy('e.date', 'ASC')
                ->getQuery();

        return $query->getResult();
    }
    /**
     * Gets all the passed events which the current user may see,
     * ordered descending (youngest event on top).
     * events occuring on the current date do **not** count as 'passed'.
     * @return AppBundle\Entity\Event[] returns an array of Event objects.
     */
    public function getPassed()
    {
        // user.isMember = 1 --> may see event.forMembers = 0/1
        // user.isMember = 0 --> may only see event.forMembers = 0
        // hence event.forMembers <= user.isMember
        $query = $this->repo->createQueryBuilder('e')
            ->where('e.date < :today AND e.isForAssociationMembers <= :userIsMember')
            ->setParameters(array(
                'today' => new \DateTime("now"),
                'userIsMember' => $this->authorization->isAssociationMember()
            ))
            ->orderBy('e.date', 'DESC')
            ->getQuery();
        return $query->getResult();
    }
}

?>
