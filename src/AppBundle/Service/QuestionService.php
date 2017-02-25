<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Event;
use AppBundle\ViewModel\Commitment\QuestionViewModelBase;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\ViewModel\Commitment\TextQuestionViewModel;
use AppBundle\ViewModel\Commitment\SelectionQuestionViewModel;

class QuestionService
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
            case YesNoQuestionViewModel::getTypeString():
                return new YesNoQuestionViewModel($q, $a);

            case SelectionQuestionViewModel::getTypeString():
                return new SelectionQuestionViewModel($q, $a);

            case TextQuestionViewModel::getTypeString():
            default:
                return new TextQuestionViewModel($q, $a);
        }
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
            } else if ($key === 0) {
                $ret["Nein"] = $value;
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }


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

    private function getMeaningfulAnswer(Answer $answer, Question $question)
    {
        if($question->getType() == YesNoQuestionViewModel::getTypeString()){
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
