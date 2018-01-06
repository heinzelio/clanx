<?php
namespace AppBundle\Service;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Department;
use AppBundle\Entity\Event;
use AppBundle\Entity\Question;
use AppBundle\ViewModel\Commitment\BaseQuestionViewModel;
use AppBundle\ViewModel\Commitment\SelectionQuestionViewModel;
use AppBundle\ViewModel\Commitment\TextQuestionViewModel;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;

interface IQuestionService
{

    /**
     * Gets the quesion viewmodel for the given quesion domainmodel.
     * @param  Question $q
     * @return BaseQuestionViewModel
     */
    public function getQuestionViewModel(Question $q, Answer $a=null);

    /**
     * gets an array of questions of the event, sorted by the id.
     * @param  Event  $event
     * @return BaseQuestionViewModel[]
     */
    public function getQuestionsSorted(Event $event);

    /**
     * gets an array of questions with answers of the event, sorted by the id.
     * @param  Event  $event
     * @return BaseQuestionViewModel[]
     */
    public function getQuestionsAndAnswersSorted(Commitment $commitment);

    /**
     * creates an array counting all answers of a given questions.
     * The key is the value of the answer.
     * @param  Question $q
     * @return array
     */
    public function countAnswers(Question $q);

    /**
     * Creates and returns a new Question
     * @param Event  $event
     * @param string $type  Type of question. use 'T' for text, 'F' for flag, 'S' for selection.
     */
    public function CreateNew(Event $event, $type='T');

    /**
     * Creates and returns a copy of all questions of the given event.
     * New questions are not assiciated with an event.
     * Call EventService.setRelations() for this.
     * @param  Event  $event
     * @return Question[]
     */
    public function getCopyOfEvent(Event $event);
}
