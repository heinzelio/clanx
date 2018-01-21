<?php
namespace App\Service;

// use Symfony\Bridge\Monolog\Logger; // TODO: Logger not working on PROD env on hostpoint. figure out why
use Doctrine\ORM\EntityManager;

use App\Entity\Answer;
use App\Entity\Commitment;
use App\Entity\Event;
use App\Entity\Question;
use App\Repository\CommitmentRepository;
use App\Service\IAuthorizationService;
use App\ViewModel\Commitment\CommitmentViewModel;

class CommitmentService implements ICommitmentService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var App\Service\IAuthorizationService
     */
    protected $authorization;

    /**
     * The repository for the Commitment entity
     * @var App\Repository\CommitmentRepository
     */
    protected $repo;

    /**
     * @var Symfony\Bridge\Monolog\Logger
     */
    // protected $logger; // TODO: Logger not working on PROD env on hostpoint. figure out why

    /**
     * @param IAuthorizationService $auth
     * @param EntityManager $em
     * @param CommitmentRepository $repository
     */
    public function __construct(
        IAuthorizationService $auth,
        EntityManager $em,
        CommitmentRepository $repository
        // Logger $logger // TODO: Logger not working on PROD env on hostpoint. figure out why
    )
    {
        $this->authorization = $auth;
        $this->entityManager = $em;
        $this->repo = $repository;
        // $this->logger = $logger; // TODO: Logger not working on PROD env on hostpoint. figure out why
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
                $a->setQuestion($this->entityManager->getReference(Question::class, $q->getId()));
                $a->setCommitment($cmt);
                $a->setAnswer($q->getAnswer());
                $this->entityManager->persist($a);
            }

            $this->entityManager->flush();
        } catch (Exception $e) {
            // TODO: Logger not working on PROD env on hostpoint. figure out why
            // $logger->debug(print_r($e->getMessage(),true));
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

        $answerRepo = $this->entityManager->getRepository(Answer::class);
        foreach ($vm->getQuestions() as $vmQuestion) { //BaseQuestionViewModel[]
            $criteria = array('question' => $vmQuestion->getId(), 'commitment' => $commitment );
            $answer = $answerRepo->findOneBy($criteria);
            if (!$answer) {
                $answer = new Answer();
                $answer->setQuestion($this->entityManager->getReference(Question::class, $vmQuestion->getId()));
                $answer->setCommitment($commitment);
                $answer->setAnswer($vmQuestion->getAnswer());
            }
            $answer->setAnswer($vmQuestion->getAnswer());
            $this->entityManager->persist($answer);
        }
        $commitment->setDepartment($vm->getDepartment());
        $this->entityManager->persist($commitment);

        try {
            $this->entityManager->flush();
            return true;
        } catch (Exception $e) {
            // TODO: Logger not working on PROD env on hostpoint. figure out why
            // $logger->debug(print_r($e->getMessage(),true));
            return false;
        }
    }

    /**
     * deletes the given commitment. Does nothing, if commitment is not definied.
     * @param  Commitment $commitment
     * @return boolean                 true if successful
     */
    public function deleteCommitment(Commitment $commitment)
    {
        if (!$commitment) {
            return;
        }

        $volunteer = $commitment->getUser();
        $em = $this->entityManager;
        foreach ($commitment->getAnswers() as $answer) {
            $em->remove($answer);
        }
        $em->remove($commitment);

        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            // TODO: Logger not working on PROD env on hostpoint. figure out why
            // $logger->debug(print_r($e->getMessage(),true));
            return false;
        }
    }
}

?>
