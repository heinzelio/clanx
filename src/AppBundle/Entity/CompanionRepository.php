<?php

// src/AppBundle/Entity/CompanionRepository.php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanionRepository extends EntityRepository
{
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
