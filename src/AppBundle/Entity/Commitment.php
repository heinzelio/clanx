<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Event;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;

/**
 * Commitment
 *
 * @ORM\Table(name="commitment", indexes={@ORM\Index(name="user_key", columns={"user_id"}), @ORM\Index(name="event_key", columns={"event_id"}), @ORM\Index(name="department_key", columns={"department_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommitmentRepository")
 */
class Commitment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Department
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Department")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;

    /**
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;

    /**
     * name of the mapped member $event
     * @var string
     */
    const EVENT = 'event';

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * name of the mapped member $user
     * @var string
     */
    const USER = 'user';

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=1000, nullable=true)
     */
    private $remark;

    /**
     * @var string
    * @ORM\Column(name="possible_start", type="string", length=200, nullable=true)
    */
    private $possibleStart;

    /**
     * @var string
    * @ORM\Column(name="shirt_size", type="string", length=10, nullable=true)
    */
    private $shirtSize;

    /**
     * @var boolean
    * @ORM\Column(name="need_train_ticket", type="boolean", nullable=false)
    */
    private $needTrainTicket = false;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set department
     *
     * @param Department $department
     *
     * @return Commitment
     */
    public function setDepartment(Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set event
     *
     * @param Event $event
     *
     * @return Commitment
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Commitment
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set remark
     *
     * @param string $remark
     *
     * @return Commitment
     */
    public function setRemark($remark)
    {
        $this->remark=$remark;
        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
    * Set possible start date
    * @param string $start
    * @return Commitment
    */
    public function setPossibleStart($start)
    {
        $this->possibleStart=$start;
        return $this;
    }

    /**
    * Get possible start date
    * @return string
    */
    public function getPossibleStart()
    {
        return $this->possibleStart;
    }

    /**
    * Set shirt size
    * @param string $shirtSize
    * @return Commitment
    */
    public function setShirtSize($shirtSize)
    {
        $this->shirtSize=$shirtSize;
        return $this;
    }

    /**
    * Get shirt size
    * @return string
    */
    public function getShirtSize()
    {
        return $this->shirtSize;
    }

    /**
    * Set value if the user needs a train ticket
    * @param boolean $needTrainTicket
    * @return Commitment
    */
    public function setNeedTrainTicket($needTrainTicket)
    {
        $this->needTrainTicket=$needTrainTicket;
        return $this;
    }

    /**
    * Get value if the user needs a train ticket
    * @return boolean
    */
    public function getNeedTrainTicket()
    {
        return $this->needTrainTicket;
    }
}
