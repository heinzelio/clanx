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
        $answers = $q->getAnswers();
        $answerStats = $this->getVoidStatistics($q);
        foreach ($answers as $answer) {
            $answerValue = $this->getMeaningfulAnswer($answer,$q);

            if(!array_key_exists($answerValue, $answerStats))
            {
                $answerStats[$answerValue] = 0;
            }
            $answerStats[$answerValue]++;
        }
        return $answerStats;
    }

    private function getMeaningfulAnswer(Answer $answer, Question $question)
    {
        if($question->getType() == YesNoQuestionViewModel::getTypeString()){
            if($answer->getAnswer()==1) {
                return "Ja";
            }else{
                return "Nein";
            }
        }
        return $answer->getAnswer();
    }
    private function getVoidStatistics(Question $q)
    {
        $arr=array();
        if($q->getType() == SelectionQuestionViewModel::getTypeString()){
            $vm = $this->getQuestionViewModel($q);
            $choices = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($vm->getChoices()));
            foreach($choices as $choice) {
              $arr[$choice] = 0;
            }
        }
        return $arr;
    }
}
