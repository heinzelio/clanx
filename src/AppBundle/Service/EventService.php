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
    /**
     * @var AppBundle\Service\DepartmentService
     */
    protected $departmentService;
    /**
     * @var AppBundle\Service\CommitmentService
     */
    protected $commitmentService;

    public function __construct(
        Authorization $auth,
        EntityManager $em,
        DepartmentService $deptService,
        CommitmentService $cmmtService
    )
    {
        $this->authorization = $auth;
        $this->entityManager = $em;
        $this->repo = $em->getRepository('AppBundle:Event');
        $this->departmentService = $deptService;
        $this->commitmentService = $cmmtService;
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

    /**
     * Collects all necessary data to fill in the "show" view of the Event page
     * @param  Event  $event [description]
     * @return [type]        [description]
     */
    public function getDetailViewModel(Event $event)
    {
        $myDepartmentsAsChief = $this->departmentService->getMyDepartmentsAsChief($event);
        $myDepartmentsAsDeputy = $this->departmentService->getMyDepartmentsAsDeputy($event);

        $enrolledCount = $this->CountVolunteersFor($event);

        $commitment = null;
        $myCommitments = $this->commitmentService->getCurrentUsersCommitmentsFor($event);
        if(count($myCommitments)>0)
        {
            $commitment = $myCommitments[0];
        }

        $mayEnroll = (!$commitment) && (!$event->getLocked());

        $mayMail = $this->authorization->maySendEventMassMail();
        $mayEdit = $this->authorization->mayEditEvent();
        $deleteAuth = $this->authorization->mayDelete($event);

        $mayDownload = $this->authorization->mayDownloadFromEvent();

        return array(
            'event' => $event,
            'mayEnroll' => $mayEnroll,
            'enrolledCount' => $enrolledCount,
            'commitment' => $commitment,
            'mayMail' => $mayMail,
            'mayEdit' => $mayEdit,
            'mayDelete' => $deleteAuth[Authorization::VALUE],
            'mayDeleteMessage' => $deleteAuth[Authorization::MESSAGE],
            'mayDownload' => $mayDownload,
            'myDepartmentsAsChief' => $myDepartmentsAsChief,
            'myDepartmentsAsDeputy' => $myDepartmentsAsDeputy,
        );
    }

    /**
     * Gets the number of people working for the given event.
     * We only count people, not commitments.
     * When the same person works in 3 departments, he still
     * counts as 1 volunteer.
     * @param  Event  $event The Eventg
     * @return integer Returns the number of commitments.
     */
    public function CountVolunteersFor(Event $event)
    {

        $cmmtRepo = $this->entityManager->getRepository('AppBundle:Commitment');
        $commitments = $cmmtRepo->findByEvent($event);

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

        $departments = $event->getDepartments();
        foreach ($departments as $dpmt) {
            $count += count($dpmt->getCompanions());
        }
        return $count;
    }

    /**
     * get all sticky events that are in the
     * future or the very near past (1 week or so)
     * @return Event[] Returns an array of event entities.
     */
    public function getEventsForMenu()
    {
        $oneWeekInterval = new \DateInterval('P7D');
        $oneWeekInterval->invert=1; // negative interval. one week back.
        $aWeekAgo = new \DateTime();
        $aWeekAgo->add($oneWeekInterval);
        $query = $this->repo->createQueryBuilder('e')
            ->where('e.sticky = 1 AND e.date > :aWeekAgo AND e.isForAssociationMembers <= :userIsMember')
            ->setParameters(array(
                'aWeekAgo' => $aWeekAgo,
                'userIsMember' => $this->authorization->isAssociationMember()
            ))
            ->getQuery();

        return $query->getResult();
    }
}

?>
