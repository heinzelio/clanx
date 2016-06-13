<?php

// src/AppBundle/Entity/CommitmentRepository.php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CommitmentRepository extends EntityRepository
{
    public function countFor($event)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(c.id)');
        $qb->from('AppBundle:Commitment','c');
        $qb->where('c.event=:evt');
        $qb->setParameter('evt',$event);

        return  $qb->getQuery()->getSingleScalarResult();
    }

    // not used anymore...
    // still here for documentation purposes
    public function existsFor($user,$event)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(c.id)');
        $qb->from('AppBundle:Commitment','c');
        $qb->where('c.event=:evt AND c.user=:usr');
        $qb->setParameter('evt',$event);
        $qb->setParameter('usr',$user);
        $count = $qb->getQuery()->getSingleScalarResult();
        return  $count > 0;
    }
}
