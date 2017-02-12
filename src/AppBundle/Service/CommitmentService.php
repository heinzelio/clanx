<?php
namespace AppBundle\Service;

use Symfony\Bridge\Monolog\Logger;
use Doctrine\ORM\EntityManager;
use AppBundle\Service\Authorization;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Answer;
use AppBundle\ViewModel\Commitment\CommitmentViewModel;

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

    /**
     * @var Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @param Authorization $auth
     * @param EntityManager $em
     * @param object $logger
     */
    public function __construct(
        Authorization $auth,
        EntityManager $em,
        Logger $logger
    )
    {
        $this->authorization = $auth;
        $this->entityManager = $em;
        $this->repo = $em->getRepository('AppBundle:Commitment');
        $this->logger = $logger;
    }

    /**
     * Gets a collection of commitments of the currently
     * logged in user for the given event.
     * @param  Event  $event
     * @return Commitment[]
     */
    public function getCurrentUsersCommitmentsFor(Event $event)
    {
        return $this->repo->findBy(array(
            Commitment::USER => $this->authorization->getUser(),
            Commitment::EVENT => $event,
        ));
    }

    /**
     * Saves the commitment for the logged in user and the
     * given event.
     * @param  Event $event
     * @param  CommitmentViewModel $vm
     * @return Commitment Returns null, if the operation failed.
     */
    public function saveCommitment(Event $event, CommitmentViewModel $vm)
    {
        $user = $this->authorization->getUser();
        $dept = $vm->getDepartment(); // Entity
        $qs = $vm->getQuestions(); // BaseQuestionViewModel[]

        $cmt = new Commitment();
        $cmt->setUser($user)->setEvent($event);
        if ($dept != null) {
            $cmt->setDepartment($dept);
        }

        try {
            $this->entityManager->persist($cmt);
            foreach ($qs as $q) { //BaseQuestionViewModel
                $a = new Answer();
                $a->setQuestion($this->entityManager->getReference('\AppBundle\Entity\Question', $q->getId()));
                $a->setCommitment($cmt);
                $a->setAnswer($q->getAnswer());
                $this->entityManager->persist($a);
            }

            $this->entityManager->flush();
        } catch (Exception $e) {
            $logger->debug(print_r($e->getMessage(),true));
            return null;
        }

        return $cmt;
    }

    /**
     * updates the commitment and its answers
     * @param  CommitmentViewModel $vm
     * @param  Commitment          $commitment
     * @return boolean true if update succeeded.
     */
    public function updateCommitment(CommitmentViewModel $vm, Commitment $commitment)
    {
        if (!$vm) {
            return;
        }

        $answerRepo = $this->entityManager->getRepository('AppBundle:Answer');
        foreach ($vm->getQuestions() as $vmQuestion) { //BaseQuestionViewModel[]
            $criteria = array('question' => $vmQuestion->getId(), 'commitment' => $commitment );
            $answer = $answerRepo->findOneBy($criteria);
            $answer->setAnswer($vmQuestion->getAnswer());
            $this->entityManager->persist($answer);
        }
        $commitment->setDepartment($vm->getDepartment());
        $this->entityManager->persist($commitment);

        try {
            $this->entityManager->flush();
            return true;
        } catch (Exception $e) {
            $logger->debug(print_r($e->getMessage(),true));
            return false;
        }
    }
}

?>
