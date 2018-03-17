<?php
namespace App\ViewModel\Event;

use App\Entity\Event;

/**
 * ViewModel for one question.
 */
class QuestionListViewModel
{
    /**
     * @var QuestionViewModel[]
     */
    private $questions;

    /**
     * @var boolean
     */
    private $mayCreate;

    /**
     * @var Event
     */
    private $event;



    /**
     * @return QuestionViewModel[]
     */
    public function getQuestions(){ return $this->questions; }

    /**
     * @param QuestionViewModel[] questions
     * @return self
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;
        return $this;
    }

    /**
     * Adds a question to the existing list of questions
     * @param QuestionViewModel $question
     */
    public function addQuestion(QuestionViewModel $question)
    {
        $this->questions[] = $question;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getMayCreate(){ return $this->mayCreate; }

    /**
     * @param boolean mayCreate
     * @return self
     */
    public function setMayCreate($mayCreate)
    {
        $this->mayCreate = $mayCreate;
        return $this;
    }


    /**
     * @return Event
     */
    public function getEvent(){ return $this->event; }

    /**
     * @param Event event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
        return $this;
    }
}
