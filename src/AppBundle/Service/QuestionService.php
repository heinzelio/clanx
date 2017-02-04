<?php
namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use AppBundle\Entity\Question;
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
}
