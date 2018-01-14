<?php

// src/AppBundle/Repository/CompanionRepository.php
namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CompanionRepository extends EntityRepository
{
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
