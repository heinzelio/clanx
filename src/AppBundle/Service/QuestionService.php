<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Event;
use AppBundle\Entity\Department;
use AppBundle\Entity\Commitment;
use AppBundle\ViewModel\Commitment\BaseQuestionViewModel;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\ViewModel\Commitment\TextQuestionViewModel;
use AppBundle\ViewModel\Commitment\SelectionQuestionViewModel;

class QuestionService implements IQuestionService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Default repository for questions.
     * @var Doctrine\ORM\EntityRepository
     */
    private $repo;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
        $this->repo = $em->getRepository('AppBundle:Question');
    }

    /**
     * Gets the quesion viewmodel for the given quesion domainmodel.
     * @param  Question $q
     * @return BaseQuestionViewModel
     */
    public function getQuestionViewModel(Question $q, Answer $a=null)
    {
        switch ($q->getType()) {
            case YesNoQuestionViewModel::staticGetTypeString():
                return new YesNoQuestionViewModel($q, $a);

            case SelectionQuestionViewModel::staticGetTypeString():
                return new SelectionQuestionViewModel($q, $a);

            case TextQuestionViewModel::staticGetTypeString():
            default:
                return new TextQuestionViewModel($q, $a);
        }
    }

    /**
     * gets an array of questions of the event, sorted by the id.
     * @param  Event  $event
     * @return BaseQuestionViewModel[]
     */
    public function getQuestionsSorted(Event $event)
    {
        $arr = array();
        foreach ($event->getQuestions() as $q) {
            $arr[$q->getId()] = $this->getQuestionViewModel($q);
        }
        ksort($arr);
        return $arr;
    }

    /**
     * gets an array of questions with answers of the event, sorted by the id.
     * @param  Event  $event
     * @return BaseQuestionViewModel[]
     */
    public function getQuestionsAndAnswersSorted(Commitment $commitment)
    {
        $arr = array();
        foreach ($commitment->getAnswers() as $a) {
            $q = $a->getQuestion();
            $arr[$q->getId()] = $this->getQuestionViewModel($q, $a);
        }
        ksort($arr);
        return $arr;
    }

    /**
     * creates an array counting all answers of a given questions.
     * The key is the value of the answer.
     * @param  Question $q
     * @return array
     */
    public function countAnswers(Question $q)
    {
        // create ordered array
        $answerStats = $this->getVoidStatistics($q);

        // fill array
        $answers = $q->getAnswers();
        foreach ($answers as $answer) {
            $answerValue = $answer->getAnswer();
            if (!array_key_exists($answerValue, $answerStats)) {
                $answerStats[$answerValue] = 0;
            }
            $answerStats[$answerValue]++;
        }

        // create readable keys
        $ret = array();
        foreach ($answerStats as $key => $value) {
            if ($key===1) {
                $ret["Ja"] = $value;
            } elseif ($key === 0) {
                $ret["Nein"] = $value;
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * Creates and returns a new Question
     * @param Event  $event
     * @param string $type  Type of question. use 'T' for text, 'F' for flag, 'S' for selection.
     */
    public function CreateNew(Event $event, $type='T')
    {
        $q = new Question();
        $q->setEvent($event)
        ->setType($type)
        ->setText('Please change text and values of this question.')
        ->setHint('Hints do not work yet.')
        ->setOptional(false);
        switch ($type) {
            case 'F':
                $q->setData('{"default":false}')
                    ->setAggregate(true);
                break;
            case 'S':
                $q->setData('{"default":"OptionA1","choices":{"Group A":{"OptionA1":"OptionA1","OptionA2":"OptionA2"},"Group B":{"OptionB1":"OptionB1"}}}')
                    ->setAggregate(true);
                break;
            case 'T':
            default:
                $q->setData('{"default":"This is the default answer."}')
                    ->setAggregate(false);
        }
        $this->entityManager->persist($q);
        $this->entityManager->flush();

        return $q;
    }

    /**
     * Creates and returns a copy of all questions of the given event.
     * New questions are not assiciated with an event.
     * Call EventService.setRelations() for this.
     * @param  Event  $event
     * @return Question[]
     */
    public function getCopyOfEvent(Event $event)
    {
        $newQuestions = array();
        foreach ($event->getQuestions() as $question) {
            $newQuestion = new Question();
            $newQuestion->setText($question->getText());
            $newQuestion->setHint($question->getHint());
            $newQuestion->setType($question->getType());
            $newQuestion->setData($question->getData());
            $newQuestion->setOptional($question->getOptional());
            $newQuestion->setAggregate($question->getAggregate());
            array_push($newQuestions, $newQuestion);
        }
        return $newQuestions;
    }

    /**
     * returns a readable text for an answer.
     * E.g.: for a boolean 1/0 return Yes/No
     * @param  Answer   $answer
     * @param  Question $question
     * @return string
     */
    private function getMeaningfulAnswer(Answer $answer, Question $question)
    {
        //TODO: use localization
        if ($question->getType() == YesNoQuestionViewModel::staticGetTypeString()) {
            if ($answer->getAnswer()=="1") {
                return "Ja";
            } else {
                return "Nein";
            }
        }
        return $answer->getAnswer();
    }

    /**
     * @param  Question $q
     * @return array
     */
    private function getVoidStatistics(Question $q)
    {
        $vm = $this->getQuestionViewModel($q);
        $arr = array();
        foreach ($vm->getSelection() as $key => $value) {
            $arr[$key] = 0;
        }
        return $arr;
    }
}
