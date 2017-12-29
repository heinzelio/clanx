<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment ;
use AppBundle\Entity\Answer ;
use AppBundle\ViewModel\Commitment\CommitmentViewModel;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\ViewModel\Commitment\TextQuestionViewModel;
use AppBundle\ViewModel\Commitment\SelectionQuestionViewModel;
use AppBundle\ViewModel\Event\EventStatisticsViewModel;
use AppBundle\ViewModel\Event\EventShowViewModel;

/**
 *
 */
interface IEventService
{
    /**
     * Gets all the upcoming events which the current user may see,
     * ordered ascending (next event on top).
     * If the event is on the current date, it counts as 'upcoming'.
     * @return AppBundle\Entity\Event[] returns an array of Event objects.
     */
    public function getUpcoming();

    /**
     * Gets all the passed events which the current user may see,
     * ordered descending (youngest event on top).
     * events occuring on the current date do **not** count as 'passed'.
     * @return AppBundle\Entity\Event[] returns an array of Event objects.
     */
    public function getPassed();

    /**
     * Collects all necessary data to fill in the "show" view of the Event page
     * @param  Event  $event
     * @return EventShowViewModel
     */
    public function getDetailViewModel(Event $event);

    /**
     * Gets the number of people working for the given event.
     * We only count people, not commitments.
     * When the same person works in 3 departments, he still
     * counts as 1 volunteer.
     * @param  Event  $event The Eventg
     * @return integer Returns the number of commitments.
     */
    public function CountVolunteersFor(Event $event);
    // TODO: camelCase

    /**
     * get all sticky events that are in the
     * future or the very near past (1 week or so)
     * @return Event[] Returns an array of event entities.
     */
    public function getEventsForMenu();

    /**
     * @param  Event  $event
     * @return CommitmentViewModel
     */
    public function getCommitmentFormViewModel(Event $event);

    public function getCommitmentFormViewModelForEdit(Commitment $commitment);

    public function getStatisticsViewModels(Event $event);

    /**
     * Creates a copy of the given event and returns int.
     * A copy is always invisible and not locked.
     * @param  Event  $event
     * @return Event
     */
    public function getCopy(Event $event);

    /**
     * Sets the relations betwenn the given event and the given departments / questions
     * @param Event $event
     * @param array $departments
     * @param array $questions
     */
    public function setRelations(Event $event, $departments=array(), $questions=array());
}
