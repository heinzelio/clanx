<?php
namespace AppBundle\ViewModel\Email;

use AppBundle\Entity\User;
use AppBundle\Entity\Event;
use AppBundle\Entity\Department;

class CommitmentConfirmationViewModel
{
	/**
	 * @var User
	 */
	public $user;

	/**
	 * @var Event
	 */
	public $event;

	/**
	 * May be null
	 * @var Department
	 */
	public $department;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return CommitmentConfirmationViewModel For method chaining.
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     * @return CommitmentConfirmationViewModel For method chaining.
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Department $department
     * @return CommitmentConfirmationViewModel For method chaining.
     */
    public function setDepartment($department)
    {
        $this->department = $department;
        return $this;
    }

}
