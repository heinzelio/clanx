<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Entity\Event;
use AppBundle\ViewModel\Commitment\CommitmentViewModel;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\ViewModel\Commitment\TextQuestionViewModel;
use AppBundle\ViewModel\Commitment\SelectionQuestionViewModel;
use AppBundle\ViewModel\Event\EventStatisticsViewModel;

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

    /**
     * @var QuestionService
     */
    protected $questionService;

    public function __construct(
        EntityManager $em,
        Authorization $auth,
        DepartmentService $deptService,
        CommitmentService $cmmtService,
        QuestionService $questionService
    )
    {
        $this->entityManager = $em;
        $this->authorization = $auth;
        $this->repo = $em->getRepository('AppBundle:Event');
        $this->departmentService = $deptService;
        $this->commitmentService = $cmmtService;
        $this->questionService = $questionService;
    }

    /**
     * Gets all the upcoming events which the current user may see,
     * ordered ascending (next event on top).
     * If the event is on the current date, it counts as 'upcoming'.
     * @return AppBundle\Entity\Event[] returns an array of Event objects.
     */
    public function getUpcoming()
    {
        $qb = $this->repo->createQueryBuilder('e');

        $assocExpression = $this->getAssociationExpression($qb, 'e');
        $dateExpression = $qb->expr()->gte('e.date', ':today');
        $qb->setParameter('today',new \DateTime("now"));

        $query = $qb
                ->where($dateExpression)
                ->andWhere($assocExpression)
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
        $qb = $this->repo->createQueryBuilder('e');

        $assocExpression = $this->getAssociationExpression($qb, 'e');
        $dateExpression = $qb->expr()->lt('e.date', ':today');
        $qb->setParameter('today',new \DateTime("now"));

        $query = $qb
                ->where($dateExpression)
                ->andWhere($assocExpression)
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
        $mayInvite = $this->authorization->maySendInvitation($event);
        $mayEdit = $this->authorization->mayEditEvent();
        $deleteAuth = $this->authorization->mayDelete($event);

        $mayDownload = $this->authorization->mayDownloadFromEvent();

        return array(
            'event' => $event,
            'mayEnroll' => $mayEnroll,
            'enrolledCount' => $enrolledCount,
            'commitment' => $commitment,
            'mayMail' => $mayMail,
            'mayInvite' => $mayInvite,
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

        $qb = $this->repo->createQueryBuilder('e');
        $stickyExpr = $qb->expr()->eq('e.sticky',1);

        $qb->setParameter(':aWeekAgo',$aWeekAgo);
        $aWeekAgoExpr = $qb->expr()->gt('e.date',':aWeekAgo');

        $associationExpression = $this->getAssociationExpression($qb, 'e');

        $query = $qb
            ->where($stickyExpr)
            ->andWhere($aWeekAgoExpr)
            ->andWhere($associationExpression)
            ->orderBy('e.date', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param  Event  $event
     * @return CommitmentViewModel
     */
    public function getCommitmentFormViewModel(Event $event)
    {
        $commitmentVM = new CommitmentViewModel();
        foreach ($event->getQuestions() as $q) {
            $qVM = $this->questionService->getQuestionViewModel($q);
            $commitmentVM->addQuestion($qVM);
        }
        return $commitmentVM->setDepartments($event->getFreeDepartments()); // TODO: dont make this on the entity. get it from a service or here.
    }

    public function getStatisticsViewModels(Event $event)
    {
        $viewModels = array();

        $qs = $event->getQuestions();
        foreach ($qs as $q) {
            if($q->getAggregate()){
                $viewModel = new EventStatisticsViewModel();
                $viewModel->setText($q->getText());
                $viewModel->setValues($this->questionService->countAnswers($q));
                $viewModels[] = $viewModel;
            }
        }
        return $viewModels;
    }

    /**
     * returns a query expression to filter the assiciationMember field.
     * When the user may see all events, the expression is simply '1=1',
     * which evaluates to 'true' in the query (suitable for AND, but not for OR).
     * @param  QueryBuilder $qb The QueryBuilder object.
     * @param string $alias The alias for the event table.
     * @return Doctrine\ORM\Query\Expr Returns an expression object.
     */
    private function getAssociationExpression(QueryBuilder $qb, $alias)
    {
        $p = 0;
        if ($this->authorization->isAssociationMember()) {
            $p = 1;
        }

        if ($this->authorization->maySeeAllEvents()) {
            return $qb->expr()->eq(1,1);
        } else {
            // user.isMember = 1 --> may see event.forMembers = 0/1
            // user.isMember = 0 --> may only see event.forMembers = 0
            // hence event.forMembers <= user.isMember
            //event.isForAssociationMembers <= :userIsMember'
            $qb->setParameter(':userIsMember', $p);
            return $qb->expr()->lte($alias . '.isForAssociationMembers',':userIsMember');
        }
    }
}

?>
