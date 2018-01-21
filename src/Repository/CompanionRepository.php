<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class CompanionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Companion::class);
    }

    // not used anymore...
    // still here for documentation purposes
    public function countFor($event)
    {
        //
        $departments = $event->getDepartments();
        $count = 0;
        foreach ($departments as $dpmt) {
            $count += count($dpmt->getCompanions());
        }
        return $count;
    }
}
