<?php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CommitmentRepository extends EntityRepository
{
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
