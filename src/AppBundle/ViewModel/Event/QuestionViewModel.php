<?php
namespace AppBundle\ViewModel\Event;

use AppBundle\Entity\Question;

/**
 * ViewModel for one question.
 */
class QuestionViewModel
{
    /**
     * @var Question
     */
    private $question;
    /**
     * @var boolean
     */
    private $mayEdit;



    /**
     * @return Question
     */
    public function getQuestion(){ return $this->question; }

    /**
     * @param Question question
     * @return self
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMayEdit(){ return $this->mayEdit; }

    /**
     * @param boolean mayEdit
     * @return self
     */
    public function setMayEdit($mayEdit)
    {
        $this->mayEdit = $mayEdit;
        return $this;
    }
}
