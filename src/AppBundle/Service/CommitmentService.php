<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\Authorization;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;

class CommitmentService
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
     * The repository for the Commitment entity
     * @var AppBundle\Repository\CommitmentRepository
     */
    protected $repo;

    public function __construct(
        Authorization $auth,
        EntityManager $em
    )
    {
        $this->authorization = $auth;
        $this->entityManager = $em;
        $this->repo = $em->getRepository('AppBundle:Commitment');
    }

    public function getCurrentUsersCommitmentsFor(Event $event)
    {
        return $this->repo->findBy(array(
            Commitment::USER => $this->authorization->getUser(),
            Commitment::EVENT => $event,
        ));
    }
}

?>
