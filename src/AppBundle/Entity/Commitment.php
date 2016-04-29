<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commitment
 *
 * @ORM\Table(name="commitment", indexes={@ORM\Index(name="user_key", columns={"user_id"}), @ORM\Index(name="event_key", columns={"event_id"}), @ORM\Index(name="department_key", columns={"department_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CommitmentRepository")
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
     * @var \AppBundle\Entity\Department
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Department")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * })
     */
    private $department;

    /**
     * @var \AppBundle\Entity\Event
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=1000, nullable=true)
     */
    private $remark;

    /**
     * @var \DateTime
    * @ORM\Column(name="possible_start", type="datetime", nullable=true)
    */
    private $possibleStart;

    /**
     * @var string
    * @ORM\Column(name="shirt_size", type="string", length=10, nullable=true)
    */
    private $shirtSize;


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
     * @param \AppBundle\Entity\Department $department
     *
     * @return Commitment
     */
    public function setDepartment(\AppBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \AppBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return Commitment
     */
    public function setEvent(\AppBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Commitment
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
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
    * @param \DateTime $start
    * @return Commitment
    */
    public function setPossibleStart($start)
    {
        $this->possibleStart=$start;
        return $this;
    }

    /**
    * Get possible start date
    * @return \DateTime
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
}
