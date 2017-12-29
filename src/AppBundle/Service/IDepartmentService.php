<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\Authorization;
use AppBundle\Entity\Event;
use AppBundle\Entity\Department;

interface IDepartmentService
{
    /**
     * Gets all the departments in the given event of which
     * the logged in user is chief
     * @param  Event $event the Event
     * @return Department[] Returns an array of department entities.
     */
    function getMyDepartmentsAsChief(Event $event);

    /**
     * Gets all the departments in the given event of which
     * the logged in user is a deputy
     * @param  Event  $event the Event
     * @return Department[] Returns an array of department entities.
     */
    function getMyDepartmentsAsDeputy(Event $event);

    /**
     * Creates and returns a copy of all departments of the given event.
     * New departments are never locked.
     * New departments are not assiciated with an event.
     * Call EventService.setRelations() for this.
     * @param  Event  $event
     * @return Department[]
     */
    function getCopyOfEvent(Event $event);
}

?>
