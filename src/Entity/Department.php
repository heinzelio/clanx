<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Department
 *
 * @ORM\Table(name="department", indexes={@ORM\Index(name="chief_user_key", columns={"chief_user_id"}), @ORM\Index(name="deputy_user_key", columns={"deputy_user_id"}), @ORM\Index(name="event_key", columns={"event_id"})})
 * @ORM\Entity
 */
class Department
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="requirement", type="string", length=200, nullable=true)
     */
    private $requirement;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \App\Entity\Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private $event;

    /**
     * The name of the mapped member $event.
     * @var string
     */
    const EVENT = 'event';

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deputy_user_id", referencedColumnName="id")
     * })
     */
    private $deputyUser;

    /**
     * The name of the mapped member $deputyUser.
     * @var string
     */
    const DEPUTY_USER = 'deputyUser';

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chief_user_id", referencedColumnName="id")
     * })
     */
    private $chiefUser;

    /**
     * The name of the mapped member $chiefUser.
     * @var string
     */
    const CHIEF_USER = 'chiefUser';

    /**
     * @ORM\OneToMany(targetEntity="Commitment", mappedBy="department")
     */
    private $commitments;

    /**
     * @ORM\OneToMany(targetEntity="Companion", mappedBy="department")
     */
    private $companions;

    /**
     * @ORM\OneToMany(targetEntity="Shift", mappedBy="department")
     */
    private $shifts;

    /**
     * @var boolean
    * @ORM\Column(name="locked", type="boolean", nullable=false)
    */
    private $locked = false;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return Department
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set requirement
     *
     * @param string $requirement
     *
     * @return Department
     */
    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * Get requirement
     *
     * @return string
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

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
     * Set event
     *
     * @var \App\Entity\Event $event
     *
     * @return Department
     */
    public function setEvent(\App\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \App\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set deputyUser
     *
     * @var \App\Entity\User $deputyUser
     *
     * @return Department
     */
    public function setDeputyUser(\App\Entity\User $deputyUser = null)
    {
        $this->deputyUser = $deputyUser;

        return $this;
    }

    /**
     * Get deputyUser
     *
     * @return \App\Entity\User
     */
    public function getDeputyUser()
    {
        return $this->deputyUser;
    }

    /**
     * Set chiefUser
     *
     * @var \App\Entity\User $chiefUser
     *
     * @return Department
     */
    public function setChiefUser(\App\Entity\User $chiefUser = null)
    {
        $this->chiefUser = $chiefUser;

        return $this;
    }

    /**
     * Get chiefUser
     *
     * @return \App\Entity\User
     */
    public function getChiefUser()
    {
        return $this->chiefUser;
    }

    public function getCommitments()
    {
        return $this->commitments;
    }

    public function getCompanions()
    {
        return $this->companions;
    }

    public function getShifts()
    {
        return $this->shifts;
    }

    /**
     * Get locked flag
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set locked flag
     *
     * @param boolean $l
     *
     * @return Department
     */
    public function setLocked($l)
    {
        if( ! $l)
        {
            $this->locked = false;
        }else{
            $this->locked = $l;
        }

        return $this;
    }

    /**
     * Returns true if the user is already a volunteer in the same department.
     * @return boolean
     */
    public function commitmentExists($commitment)
    {
        foreach ($this->getCommitments() as $c) {
            if($c->getUser()->getId()==$commitment->getUser()->getId())
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets a string representing the department
     * @return string
     */
    public function __toString()
    {
        return strval($this->name);
    }

    /**
     * Gets a string representation of the department
     * including the requirement.
     * @return string
     */
    public function getLongText($value='')
    {
        if ($this->requirement)
            return $this->name . ' (' . $this->requirement . ')';
        else
            return $this->name;
    }
}
