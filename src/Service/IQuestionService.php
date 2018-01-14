<?php
namespace App\Service;

use App\Entity\Answer;
use App\Entity\Commitment;
use App\Entity\Department;
use App\Entity\Event;
use App\Entity\Question;
use App\ViewModel\Commitment\BaseQuestionViewModel;
use App\ViewModel\Commitment\SelectionQuestionViewModel;
use App\ViewModel\Commitment\TextQuestionViewModel;
use App\ViewModel\Commitment\YesNoQuestionViewModel;

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
