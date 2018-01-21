<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use App\Entity\Commitment;

class CommitmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commitment::class);
    }
    // not used anymore...
    // still here for documentation purposes
    public function countFor($event)
    {
        // We only count people, not commitments.
        // When the same person works in 3 departments, he still
        // counts as 1 volunteer.
        $em = $this->getEntityManager();
        $repo = $em->getRepository(Commitment::class);
        $commitments = $repo->findByEvent($event);
        $userIds = array();
        $count=0;
        foreach ($commitments as $c) {
            $uid = (string)$c->getUser()->getId();
            if(! isset($userIds[$uid]))
            {
                $count++;
                $userIds[$uid] = 1;
            }
        }

        return $count;
    }

    // not used anymore...
    // still here for documentation purposes
    public function existsFor($user,$event)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(c.id)');
        $qb->from(Commitment::class,'c');
        $qb->where('c.event=:evt AND c.user=:usr');
        $qb->setParameter('evt',$event);
        $qb->setParameter('usr',$user);
        $count = $qb->getQuery()->getSingleScalarResult();
        return  $count > 0;
    }
}
