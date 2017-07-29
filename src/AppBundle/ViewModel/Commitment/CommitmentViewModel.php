<?php

namespace AppBundle\ViewModel\Commitment;

use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * user that made the commitment
     * @var User
     */
    private $user;

    /**
     * id
     * @var integer
     */
    private $id;

    /**
     * Gets the department.
     * @return Department The department.
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

    /**
     * @Assert\Callback()
     *
     * Validation callback method (defined in base class)
     * @param  ExecutionContextInterface $context
     */
    public function validateAnswer(ExecutionContextInterface $context)
    {
        foreach ($this->getQuestions() as $q ) {
            $q->validateAnswer($context);
        }
    }

    /**
     * Get the user that made the commitment
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the user that made the commitment
     *
     * @param User user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the value of id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     * @param integer id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

}
