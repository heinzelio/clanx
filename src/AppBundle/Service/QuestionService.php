<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Entity\Question;
use AppBundle\Entity\Answer;
use AppBundle\ViewModel\Commitment\QuestionViewModelBase;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\ViewModel\Commitment\TextQuestionViewModel;
use AppBundle\ViewModel\Commitment\SelectionQuestionViewModel;

class QuestionService
{
    /**
     * Gets the quesion viewmodel for the given quesion domainmodel.
     * @param  Question $q
     * @return BaseQuestionViewModel
     */
    public function getQuestionViewModel(Question $q)
    {
        switch ($q->getType()) {
            case YesNoQuestionViewModel::getTypeString():
                return new YesNoQuestionViewModel($q);

            case SelectionQuestionViewModel::getTypeString():
                return new SelectionQuestionViewModel($q);

            case TextQuestionViewModel::getTypeString():
            default:
                return new TextQuestionViewModel($q);
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

    private function getMeaningfulAnswer(Answer $answer, Question $question)
    {
        if($question->getType() == YesNoQuestionViewModel::getTypeString()){
            if ($answer->getAnswer()==1) {
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
