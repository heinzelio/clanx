<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\Authorization;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;

class UserService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * The repository for the User entity
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repo;

    public function __construct(
        EntityManager $em
    )
    {
        $entityManager = $em;
        $this->repo = $em->getRepository('AppBundle:User');
    }

    /**
     * Gets all users that are association members
     * @return Returns an array of users.
     */
    public function getAllAssociationMembers()
    {
        $qb = $this->repo->createQueryBuilder('u');

        $assocFilter = $qb->expr()->eq('u.isAssociationMember',1);

        $qb->where($assocFilter)
            ->orderBy('u.surname')
            ->addOrderBy('u.forename');

        return $qb->getQuery()->getResult();
    }

    /**
     * Gets an array of all Users.
     * @return Returns an array of User entities.
     */
    public function getAllUsers()
    {
        return $this->repo->findAll();
    }
}

?>
