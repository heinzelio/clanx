<?php

namespace AppBundle\Entity;

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
     *   @ORM\JoinColumn(name="deputy_user_id", referencedColumnName="id")
     * })
     */
    private $deputyUser;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chief_user_id", referencedColumnName="id")
     * })
     */
    private $chiefUser;



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
     * @param \AppBundle\Entity\Event $event
     *
     * @return Department
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
     * Set deputyUser
     *
     * @param \AppBundle\Entity\User $deputyUser
     *
     * @return Department
     */
    public function setDeputyUser(\AppBundle\Entity\User $deputyUser = null)
    {
        $this->deputyUser = $deputyUser;

        return $this;
    }

    /**
     * Get deputyUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getDeputyUser()
    {
        return $this->deputyUser;
    }

    /**
     * Set chiefUser
     *
     * @param \AppBundle\Entity\User $chiefUser
     *
     * @return Department
     */
    public function setChiefUser(\AppBundle\Entity\User $chiefUser = null)
    {
        $this->chiefUser = $chiefUser;

        return $this;
    }

    /**
     * Get chiefUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getChiefUser()
    {
        return $this->chiefUser;
    }
}
