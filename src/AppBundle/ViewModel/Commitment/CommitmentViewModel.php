<?php

namespace AppBundle\ViewModel\Commitment;

use AppBundle\Entity\Department;

/**
 * A view model for the commitment form.
 */
class CommitmentViewModel
{
    /**
     * The department.
     * @var Department
     */
    private $department;

    /**
     * @var Department[]
     */
    private $departments =  array();

    /**
     * An array of questions for this commitment
     * @var BaseQuestionViewModel[]
     */
    private $questions = array();

    /**
     * Gets the department.
     * @return department The department.
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Sets the department.
     * @param department $department The department.
     * @return CommitmentViewModel
     */
    public function setDepartment($department)
    {
        $this->department = $department;
        return $this; // for setter chains
    }

    /**
     * Gets the questions.
     * @return BaseQuestionViewModel[] The questions.
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Sets the questions
     * @param BaseQuestionViewModel[] $questions The questions.
     * @return CommitmentViewModel
     */
    public function setQuestions($questions)
    {
        $this->questions = array();
        foreach ($questions as $q) {
            $this->questions[$q->getId()] = $q;
        }
        return $this; // for setter chains
    }

    /**
     * @param BaseQuestionViewModel $question
     * @return CommitmentViewModel
     */
    public function addQuestion(BaseQuestionViewModel $question)
    {
        $this->questions[$question->getId()] = $question;
        return $this; // for setter chains
    }

    /**
     * @return Department[]
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * @param Department[] $departments
     * @return CommitmentViewModel
     */
    public function setDepartments($departments)
    {
        $this->departments = $departments;
        return $this; // for setter chains
    }

    /**
     * @return boolean
     */
    public function hasDepartments()
    {
        return count($this->departments)>0;
    }

}
